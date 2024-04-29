<template>
  <div class="toolbar">
    <User :state="state" />
    <button @click="state.loadFiles()" class="toolbarButtons">
      upload
    </button>
    <div class="menu" ref="menu" v-if="!memo.length && state.loggedIn">
      <div class="parent" style="z-index: 1900; width: 145px">
        asset tools
        <div class="sub" @click="console.log(Math.PI)" STYLE="width: 300px; height:47px;">
          <button
            @click="state.selectAll()"
            class="toolbarButtons"
            :disabled="allSelected"
            title="select all [ctrl + a]"
            :class="{'buttons' : !allSelected, 'disabledButton' : allSelected}"
          >
            select all
          </button>
          <button
            @click="state.deSelectAll()"
            class="toolbarButtons"
            :disabled="!someSelected"
            title="de-select all [ctrl + shift + a]"
            :class="{'buttons' : someSelected, 'disabledButton' : !someSelected}"
          >
            deselect all
          </button>
        </div>
        <div class="sub" style="z-index: 1700;width: 375px;height: 69px;" @click="console.log('this menu item')">
          <span style="margin-left: 5px; font-size:.8em;">[w/selected&rarr;]</span>
          <button
            class="toolbarButtons visibilityButton"
            style="min-width: 50px;min-height: 50px;"
            :disabled="!someSelected"
            @click.prevent.stop="state.setLinkPropertySelected('private', 1)"
            :class="{'actionButton' : someSelected,
                     'disabledButton' : !someSelected,
                     'private': someSelected,
                     'privateDisabled': !someSelected}"
            :title="`set visibility to HIDDEN (from public galleries), for all selected`"
          ></button>
          <button
            class="toolbarButtons visibilityButton"
            style="min-width: 50px;min-height: 50px;"
            :disabled="!someSelected"
            @click.prevent.stop="state.setLinkPropertySelected('private', 0)"
            :class="{'actionButton' : someSelected,
                     'disabledButton' : !someSelected,
                     'notPrivate': someSelected,
                     'notPrivateDisabled': !someSelected}"
            :title="`set visibility to VISIBLE (from public galleries), for all selected`"
          ></button>
          <button
            @click="state.deleteSelected()"
            class="toolbarButtons"
            :disabled="!someSelected"
            title="delete selected [del]"
            :class="{'deleteButton' : someSelected, 'disabledButton' : !someSelected}"
          >
            delete
          </button>
        </div>
        <div class="sub" @click.stop.prevent style="width: 175px;">
          <PageSel :state="state" />
        </div>
        <div
          class="sub"
          style="z-index: 1700;width: 375px;height: 44px;"
            v-if="someSelected"
          >
          <span style="margin-left: 5px; font-size:.8em;">[w/selected&rarr;]</span>
          <CollectionSelection :state="state" :links="filteredLinksForCollectionSelection"  :mode="'multi'" :someSelected="someSelected"/>
        </div>
        <div
          class="sub"
          style="z-index: 1700;width: 375px;height: 44px;line-height: 16px;padding-top: 4px; font-size: .8em;"
          v-else
        >
          select multiple items, then check here<br>for bulk assign-to-collection buttons
        </div>
        <div
          title="view my stats, including views etc"
          class="sub"
          @click="state.getUserStats(state.loggedinUserID)"
        >
          my stats
        </div>
        <div class="sub" style="z-index: 100; min-width: 180px;" @click="state.showCollections=true">
          my collections
          <div
            class="sub2"
            v-for="collection in state.collections"
            @click.stop.prevent="viewCollection(collection)"
          >
            <button
              class="toolbarButtons collectionsButton"
              style="color: #fff; min-width: unset; height: 24px; background: #84fd;margin: unset;margin-right:5px;"
              @click.stop.prevent="viewCollection(collection)"
              title="view this collection"
            >👁</button>
            <button
              class="toolbarButtons collectionsButton"
              style="height: 24px; margin: unset; margin-right:5px;min-width: unset"
              @click.stop.prevent="state.showEditCollection(collection)"
              title="edit this collection"
            >✎</button>
            <span>{{state.shortText(collection.name, 16)}}</span>
          </div>
        </div>
        <div class="sub" @click="">
          submenus [unused]
          <div class="sub2" @click="window.open('https://gifs.twilightparadox.com/df0898b287292aab8cf29efb75cb1783.gif', '_blank')">easter egg 1!</div>
          <div class="sub2" @click="window.open('https://whr.rf.gd/a/full/2whT', '_blank')">easter egg 2!</div>
          <div class="sub2" @click="window.open('https://emphasis.bizuit.com/?c=the%20super%20secret','_blank')">easter egg 3!</div>
          <div class="sub2" @click="window.open('https://srmcgann.github.io/spaceflex','_blank')">easter egg 4!</div>
        </div>
      </div>
    </div>
    <input
      type="text"
      autofocus
      ref="uploadURL"
      class="URLinput"
      @keydown.stop="keydown($event)"
      @keypress.enter="uploadByURL()"
      v-model="state.uploadFromURL"
      placeholder="or, upload from a URL... it might work!"
    >
    <button @click="uploadByURL()" class="goButton" title="download asset by URL [enter]">go</button>
  </div>
</template>

<script>
import User from './User'
import PageSel from './PageSel'
import CollectionSelection from './CollectionSelection'

export default {
  name: 'Toolbar',
  props: [ 'state' ],
  components: {
    User,
    PageSel,
    CollectionSelection
  },
  data(){
    return {
      memo: [],
      window
    }
  },
  computed: {
    filteredLinksForCollectionSelection(){
      let links = []
      let ct = 0
      this.state.links.map(v=>{
        if(v.selected) {
          links = [...links, v]
          ct++
        }
      })
      this.state.userLinks.map(v=>{
        if(v.selected) {
          links = [...links, v]
          ct++
        }
      })
      this.state.linksSelectedTally = ct
      return links
    },
    someSelected(){
      return this.state.links.filter(v=>v.selected).length || this.state.userLinks.filter(v=>v.selected).length
    },
    allSelected(){
      return (this.state.links.filter(v=>v.selected).length + this.state.userLinks.filter(v=>v.selected).length) == this.state.links.length + this.state.userLinks.length
    }
  },
  methods: {
    viewCollection(collection){
      this.state.previewPosition = 0
      this.state.viewCollection(collection, 0)
    },
    keydown(e){
      if(e.keyCode == 46 || e.keyCode == 17 || this.state.keys[17] || e.keyCode == 18 || this.state.keys[18]) this.state.onkeydown(e)
    },
    uploadByURL(){
      let URL = this.state.uploadFromURL
      if(!URL) {
        this.$refs.uploadURL.focus()
        return
      }
      let assetFileName = this.state.uploadFromURL.split('/')
      assetFileName = decodeURIComponent(assetFileName[assetFileName.length-1].split('?')[0])
      this.state.modalContent = `<br><br><br><br><br><br>importing asset<br><br><div style="color: #f80">${assetFileName}<br><br></div>`
      this.state.showLoading=true
      this.state.showModal = true
      let batchMetaData = {
        loggedIn: this.state.loggedIn,
        userID: this.state.loggedinUserID,
        passhash: this.state.passhash,
        description: '',
      }
      if(URL){
        let sendData = {
          userName: this.state.loggedinUserName,
          passhash: this.state.passhash,
          URL,
          batchMetaData
        }
        fetch(`${this.state.URLbase}/` + 'uploadFromURL.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          if(data[0]){
            if(data[0]){
              this.state.uploadFromURL = ''
              data[1].map((v, j)=>{
                this.state.addLink(data[2][j], data[3][j], j, v, false, this.state.loggedinUserID, data[6][j], data[7][j], data[8][j], data[9], data[10][j], data[11][j], data[12][j],data[13][j], data[14][j], data[15][j])
                this.state.previewLink = this.state.links[this.state.links.length-1]
                this.state.showPreview = true
              })
            }
            this.state.modalContent = ''
            this.state.closeModal()
            if(this.state.loggedIn){
              this.state.links = []
              this.state.fetchUserLinks(this.state.loggedinUserID)
              this.state.jumpToPage(0)
              this.state.uploadEventTally++
              /*this.state.modalContent = '<div style="box-sizing: border-box;min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #6666; color: #0f8; padding: 100px; text-align: left;">' + `imported asset:<br><br>${data[8][0]}` + '</div>'
              this.state.showModal = true*/
            }else{
              if(data[0]){
                this.state.modalContent = '<div style="box-sizing: border-box;min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #0846; color: #8f8; padding: 100px; text-align: left;word-break: auto-phrase;">' + `excellent choice, uploading here...<br><br>you have not logged in though, which means your links will be lost soon.<br><br>If you register, nothing is needed except a name of your choosing and a password you will remember, then these uploads will be auto-saved to your new profile...<br><br>just don't close the page or refresh before you login or register!<br><br><br>thanks for using this service!` + '</div>'
                this.state.showModal = true
                this.state.showRegister = true
                this.state.showLoginPrompt = true
                this.state.uploadEventTally++
              }else{
                this.state.modalContent = '<div style="min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #8002; color: #f88; padding-top: 100px;">' + data[5] + '</div>'
              }
            }
          }else{
            this.state.modalContent = '<div style="box-sizing: border-box;min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #6006; color: #f00; padding: 100px; text-align: left;">' + `failed to import asset:<br><br>error: &rarr;${data[5]}` + '</div>'
            this.state.showModal = true              
          }
          this.$refs.uploadURL = ''
          //this.$refs.uploadURL.focus()
          this.state.showLoading = false
        })
      }
    }
  },
  mounted(){  
  }
}
</script>

<style scoped>
  .toolbar{
    background: linear-gradient(90deg, #402, #111c, #3333);
    color: #4fa;
    font-size: 20px;
    position: absolute;
    top: 52px;
    z-index: 100;
    width: calc(100vw);
    padding-right: 20px;
  }
  .toolbarSection{
    display: inline-block;
    border: 4px solid #40fa;
  }
  .toolbarButtons{
    margin: 5px;
    min-width: 100px;
    height: 30px;
    padding: 2px;
    padding-bottom: 0;
    border-radius: 5px;
    vertical-align: middle;
    background-size: 45px;
  }
  .deleteButton{
    color: #f88;
    background-color: #400d;
    border-color: #8008;
  }
  .actionButton{
    color: #f88;
    background-color: #042d;
    border-color: #0808;
    border-radius: 40%;
  }
  .disabledButton{
    color: #888;
    background-color: #333d;
    border-color: #0008;
  }
  .goButton{
    min-width: unset;
    width: 36px;
    height: 26px;
    margin-left: 6px;
    padding: 0;
    vertical-align: middle;
  }
  .URLinput{
    font-size: 16px;
    margin-top: 5px;
    font-size: 10px;
    background: #001;
    color: #4f8;
    width: 255px;
    display: inline-block;
    border-radius: 5px;
    height: 30px;
    border: 3px solid #40f8;
  }
  .menu{
    background: #40f2;
    background: unset;
    top: -4px;
    display: inline-block;
    border: none;
    left: 417px;
    text-shadow: 2px 2px 4px #000;
    box-sizing: border-box;
    margin-right: 5px;
  }
  .sub{
    line-height: 36px;
    margin-left: 33px;
    background: #036d;
    text-align: left;
    min-height: 40px;
    min-width: 160px;
    padding-left: 10px;
  }
  .sub2 {
    margin-left: 66px;
    background: #330d;
    text-align: left;
    padding-left: 10px;
    min-height: 30px;
    min-width: 300px;
  }
  .sub, .sub2{
    position: relative;
    display: none;
    z-index: 10;
    margin-top: 0px;
    border: 4px solid #fff2;
  }
  .parent{
    border: 3px solid #fff2;
    position: relative;
    max-width: 200px;
    vertical-align: middle;
    cursor: pointer;
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 2px;
    text-align: center;
    height: 30px;
    font-size: 18px;
    margin: 0px;
    display: inline-block;
    z-index: 0;
    background: #022d;
  }
  .parent:hover .sub {
    display: block;
  }
  .sub:hover .sub2 {
    display: block;
  }
  .parent:hover{
    background: #044d;
    color: #fff;
  }
  .sub:hover {
    background: #068d;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
  }
  .sub2:hover {
    background: #660d;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
  }

</style>
