<template>
  <div class="main" ref="main" tabindex="0">
    <div v-if="showUploadProgress" class="uploadProgressContainer">
      <br>uploading {{filesUploading.length}} file{{filesUploading.length > 1 ? 's':''}}...
      <LoadingAnimation
        class="progressBar"
        v-for="file in filesUploading"
        :state="state"
        :percent="file.perc/100"
        :filename="state.shortText(file.uploadName, 30)"
      />
    </div>
    <div
      v-if="state.showUploadModal"
      class="uploadModal"
      :class="{'dragover': dragover}"
      @drop.prevent="processDrop"
      @dragover.prevent="dragOverHandler"
      @dragleave.prevent="dragLeaveHandler"
    >
      <div class="uploadModalInner">
        <span style="font-size: 2em;color: #888">upload tracks</span><br><br>
        <br>valid formats<br><br>
        MP3, OGG, WAV<br><br><br>
        <span style="font-size: 1.4em;color: #ff0">drop file(s) here...</span><br>
        <br>or<br><br>
        <button @click="uploadManual()" style="background: #0f0;max-width: 400px;">manually select one from your device</button><br><br>
        <button @click="state.showUploadModal = false" style="background: #400;color: #fee;font-weight: 400;">cancel</button>
      </div>
    </div>
    <div
      id="dropTarget"
      class="dropTarget"
      @dragover.prevent
      @drop.prevent="dropFiles($event)"
      @click=""
      ref="dropTarget"
    >
      <div
        v-if="showFeatured"
        class="featuredItems"
      >
        <div
          v-for="item in state.featuredItems"
          v-html="item.slug"
          class="featuredItem"
        ></div>
      </div>
      <div
        class="dropTargetInner"
        :style="showFeatured ? 'width:400px' : ''"
      >
        <div
          ref="dropTargetCaption"
          id="dropTargetCaption"
          v-if="showFeatured"
          style="cursor: pointer;"
          :style="state.mode=='trending'"
          @click="this.loadFiles()"
        >
          throw sum filez [click/drop]<br><br>
          accepted : <font style="color: #ff0;">gif<br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;web[p/m]<br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;png<br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;jp[e]g<br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;mp4/mkv<br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;mp3/ogg/wav<br><br></font>
          max size : <font style="color: #ff0;">25MB<br></font>
          <span style="font-size: .7em;">(per file)</span><br><br>
          max files: <font style="color: #ff0;">15<br></font>
          <span style="font-size: .7em;">(at a time)</span><br><br><br>
          <div style="font-size: .6em;">
          WARRANTY: <font style="color: #ff0;">none<br><br></font>
          <span style="color: #f00;font-size: 1.2em;">
            <b>all anonymous uploads will<br>be deleted after 24 hours!</b>
          </span><br>
          this website is a work-in-progress.<br>
          your files will likely be deleted anyway :D</div>
        </div>
        <div
          v-if="(state.mode=='default' || state.mode=='user') && !state.showPreview && !state.showAdmin && (state.links.length || state.userLinks.length)"
          class="links"
          
        >
          <Link
            :state="state"
            :omitAssetData="false"
            v-for="link in state.links"
            :link="link"
            :linkMode="'link'"
            :key="link.id"
            v-if="state.links.length"
          />
          <Link
            :state="state"
            :omitAssetData="false"
            v-for="link in state.userLinks"
            :link="link"
            :linkMode="'userLink'"
            :key="link.id"
            v-if="state.userLinks.length"
          />
        </div>
        <div
          v-if="state.mode=='user' && !state.userLinks.length && !state.loadingAssets"
          style="text-align: center;"
        >
          <br><br><br><br>
          <span style="font-size: 2em;">4 0 4</span>
          <br><br><br><br><br>
          this user has no<br>public items at this time
        </div>
        <div v-if="state.mode=='col' || state.mode=='item' && !state.showPreview && !state.showAdmin && state.miscLinks.length" class="links">
          <Link
            :state="state"
            :omitAssetData="false"
            v-for="link in filteredLinks"
            :link="link"
            :linkMode="'userLink'"
            :key="link.id"
            v-if="state.miscLinks.length"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Link from './Link'
import LoadingAnimation from './LoadingAnimation'

export default {
  name: 'Main',
  props: [ 'state' ],
  components: {
    Link,
    LoadingAnimation,
  },
  data(){
    return {
      preload: [],
      rejects: [],
      dragover: false,
      showUploadProgress: false,
      filesUploading: []
    }
  },
  computed:{
    showFeatured(){
      return this.state.mode == 'trending' ||
             !(this.state.userLinks.length ||
             this.state.links.length ||
             this.state.loadingAssets)
    },
    filteredLinks(){
      return this.state.miscLinks.filter(v=>v)
    }
  },
  methods: {
    shortUploadName(str){
      let ret = str
      if(ret.length > 43) ret = ret.substring(0, 30) + '...' + ret.substring(ret.length-10)
      return ret
    },
    dragLeaveHandler(){
      this.dragover = false
    },
    dragOverHandler(){
      this.dragover = true
    },
    processUpload(files){
    
      if(!files.length) return
      this.showUploadProgress = true
      this.$refs.main.style.zIndex = 100000
      this.filesUploading = Array(files.length).fill().map(v=>{return {}})
      console.log(files)
      Array.from(files).forEach((v, i)=>{
        v.completed = false
        this.filesUploading[i].perc = 0
        this.filesUploading[i].uploadName = v.name
      })
        
      let ct = 0
      files.map((file, i) => {
        console.log(`file ${i}: `, file)
        if((
          file.type == 'image/gif' ||
          file.type == 'image/jiff' ||
          file.type == 'image/jpeg' ||
          file.type == 'image/jpg' ||
          file.type == 'image/png' ||
          file.type == 'image/webp' ||
          file.type == 'video/mp4' ||
          file.type == 'video/webm' ||
          file.type == 'video/mkv' ||
          file.type == 'audio/mp3' ||
          file.type == 'audio/wav' ||
          file.type == 'audio/mpeg') &&
          file.size < 25000000){
          ct++
        } else {
          this.rejects = [...this.rejects, file]
        }
      })
      
      let rej = '<div style="min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #4008; color: #f88; padding-top: 100px;">'
      this.rejects.map(reject=>{
        let sz = (reject.size/(1024**2)|0).toLocaleString('en-us') + ' MB<br>'
        rej += `oversized/rejected: size: ${sz} "${reject.name}" <br><br>`
      })
      if(this.rejects.length) {
        this.state.modalQueue = [...this.state.modalQueue, rej + '</div>']
        this.state.closeModal()
      } else if(ct) {
        Array.from(files).forEach((v, i) => {
          let batchMetaData = {
            loggedIn: this.state.loggedIn,
            userID: this.state.loggedinUserID,
            passhash: this.state.passhash,
            description: '',
          }
          console.log('batchMetaData', batchMetaData)
          let data = new FormData()
          data.append(`uploads_${0}`, v)
          //data.append('file', v)
          data.append('batchMetaData', JSON.stringify(batchMetaData))
          let request = new XMLHttpRequest()
          request.open('POST', `${this.state.URLbase}/` + 'upload.php')
          request.upload.addEventListener('progress', e => {
            let perc = (e.loaded / e.total)*100
            this.filesUploading[i].uploadName = v.name
            this.filesUploading[i].perc = perc
            let preFinish = true
            this.filesUploading.map(v=>{
              if(v.perc<99) preFinish = false
            })
            if(preFinish) {
              console.log('showing loading...')
              this.state.showLoading = true
            }
          })
          request.addEventListener('load', e=> {
            v.completed = true

            if(e.currentTarget.responseText[0] != '<'){
              let data = JSON.parse(e.currentTarget.responseText)
              if(data[0]){
                data[1].map((v, j)=>{
                  this.state.addLink(data[2][j], data[3][j], i, v, false, this.state.loggedinUserID, data[6][j], data[7][j], data[8][j], data[9], data[10][j], data[11][j], data[12][j],data[13][j],data[14][j], data[15][j])
                })
              }
            }
            
            let finished = true
            Array.from(files).forEach(q=>{
              if(!q.completed) finished = false
            })
            
            if(finished) {
              console.log('finished')
              this.showUploadProgress = false
              this.$refs.main.style.zIndex = 0
              if(this.state.loggedIn){
                this.state.modalContent = ''
                this.state.closeModal()
                this.state.links = []
                this.state.fetchUserLinks(this.state.loggedinUserID)
                this.state.jumpToPage(0)
                this.state.uploadEventTally++
              }else{
                let data = JSON.parse(e.currentTarget.responseText)
                if(data[0]){
                  this.state.modalContent = '<div style="box-sizing: border-box;min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #0846; color: #8f8; padding: 100px; text-align: left;word-break: auto-phrase;">' + `excellent choice, uploading here...<br><br>you have not logged in though, which means your links will be lost soon.<br><br>If you register, nothing is needed except a name of your choosing and a password you will remember, then these uploads will be auto-saved to your new profile...<br><br>just don't close the page or refresh before you login or register!<br><br><br>thanks for using this service!` + '</div>'
                  this.state.showModal = true
                  this.state.showRegister = false
                  this.state.showLoginPrompt = true
                }else{
                  this.state.modalContent = '<div style="min-width:90vw; min-height: 50vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);background: #8002; color: #f88; padding-top: 100px;">' + data[5] + '</div>'
                }
              }
            }
          })
          request.send(data)
        })
      }else{
        this.$refs.main.style.zIndex = 0
        alert('no files were uploaded. hmmmm. mebbe too big tho')
        this.state.closeModal()
        this.showUploadProgress = false
      }
    },
    dropFiles(e){

      /*let files = document.createElement('input')
      files.type = 'file'
      files.name = 'uploads[]'
      files.multiple = true
      files.accept = 'image/gif, image/jiff, image/jpeg, image/jpg, image/png, image/webp, video/mp4, video/webm, video/mkv, audio/mp3, audio/wav, audio/mpeg'
      files.files = []*/
      
      let files = []
      if(e.dataTransfer.items){
        [...e.dataTransfer.items].forEach((item, i) => {
          if (item.kind === "file") {
            const file = item.getAsFile()
            files = [...files, file]
          }
        })
      }else{
        files = Array.from(e.dataTransfer.files)
      }
      if(files.length) this.processUpload(files)
    },
    loadFiles(){
      //if(this.state.links.length) return
      // uncomment above to disable subsequent
      // click->open-file-dialog, after 1 successful upload
      
      let files = document.createElement('input')
      files.type = 'file'
      files.multiple = true
      files.accept = 'image/gif, image/jiff, image/jpeg, image/jpg, image/png, image/webp, video/mp4, video/webm, video/mkv, audio/mp3, audio/wav, audio/mpeg'
      files.onchange = () => this.processUpload(Array.from(files.files))
      files.click()
    }
  },
  mounted(){
    //this.$refs.main.focus()
    this.state.loadFiles = this.loadFiles
    this.preload = [ ...this.preload,
      [document.createElement('video'), 'loading.mp4'],
      [new Image(), '../assets/nonvisible.png'],
    ]
    this.preload.map(v => {
      v[0].style.position='absolute'
      v[0].style.zIndex='-1'
      v[0].style.opacity='0.001'
      document.body.appendChild(v[0])
      v[0].src = this.state.URLbase + '/' + v[1]
    })
  }
}
</script>

<style scoped>
  .main{
    background-color: #000011ee;
    font-size: 20px;
    overflow-y: scroll;
    overflow-x: hidden;
    height: 100%;
    padding: 0px;
    box-sizing: border-box;
    text-align: center;
    position: absolute;
    padding-top: 120px;
    z-index: 0;
    padding-bottom: 400px;
    width: 100vw;
  }
  /*.dropTarget:hover{
    background-color: #0648;
    cursor: pointer;
  }*/
  .dropTarget{
    color: #0f8;
    display: inline-block;
    text-align: left;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 100% 100%;
    border-radius: 10px;
    width: 100%;
    min-width: 705px;
    min-height: calc(100% - 10px);
    box-sizing: border-box;
  }
  .dropTargetInner{
    width: 400px;
  }
  .links{
    margin: 0;
    box-sizing: border-box;
    text-align: center;
    display: flex;
    flex-wrap: wrap;
    row-gap: 30px;
    column-gap: 30px;
    align-items: flex-start;
    justify-content: space-evenly;
  }
  #dropTargetCaption{
    display: inline-block;
    width: 400px;
    position: fixed;
    left: 50%;
    top: calc(50% - 64px);
    transform: translate(-50%, -50%);
    padding: 38px;
    border-radius: 32%;
    height: 320px;
    padding-top: 0;
    padding-right: 28px;
    background-color: #103c;
    box-shadow: 0 0 150px 150px #103c;
  }
  .uploadModal{
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: #001d;
    z-index: 100000;
    line-height: 1.05em;
  }
  .uploadProgressContainer{
    position: fixed;
    top: 0;
    left: 0;
    overflow: auto;
    width: 100vw;
    height: 100vh;
    background: #102d;
    z-index: 100000;
    line-height: 1.05em;
    color: #f00;
    font-size: 24px;
  }
  .uploadModalInner{
    position: absolute;
    top: calc(50% - 60px);
    left: 50%;
    width: 400px;
    height: 280px;
    background: #103b;
    z-index: 100000;
    box-shadow: 0px 0px 100px 100px #103b;
    transform: translate(-50%, -50%);
    border-radius: 10px;
  }
  .dragover{
    background: #0246;
  }
  .progressBar{
    width: 800px;
    margin-left: auto;
    margin-right: auto;
  }
  .progressBarInner{
    background: #40f8;
    height: 100%;
  }
  .progressBarInnerOutline{
    border: 1px solid #84f3;
    height: 100%;
  }
  .featuredItem{
    display: inline-block;
  }
  .progressText{
    position: relative;
    font-size: 17px;
    color: #dbf;
    right: 50%;
    transform: translate(50%, -85%);
    text-shadow: 1px 1px 2px #000;
    display: inline-block;
  }
  
  .featuredItems{
    width: 300px;
  }
</style>

