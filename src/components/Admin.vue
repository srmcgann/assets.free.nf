<template>
  <div
    class="admin"
    ref="admin"
    tabindex="1000"
    v-if="state.showAdmin"
  >
    <div class="modalInner" v-if="state.adminData">
      <div class="adminSection" v-if="state.adminData?.footprint">
        <table>
          <tr>
            <th>footprint</th>
            <th>files</th>
            <th>orphans</th>
          </tr>
          <tr>
            <td class="td" v-html="state.size(state.adminData.footprint)"></td>
            <td class="td" v-html="state.adminData['number assets'].toLocaleString()"></td>
            <td class="td" v-html="state.adminData['orphaned assets'].length"></td>
          </tr>
        </table>
      </div>
      <div class="adminSection" v-if="state.adminData?.fileSizes">
        Actual Assets, on drive<br>
        <table>
          <tr>
            <th>slug</th>
            <th>preview</th>
            <th>trending</th>

            <!--
            <th>
              <button
                :class="{'sortCol': sortMode=='views'}"
                @click="setSortMode('views')"
                v-html="`views<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>
            -->
            
            <th>
              <button
                :class="{'sortCol': sortMode=='sizes'}"
                @click="setSortMode('sizes')"
                v-html="`size<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>
            <th>
              <button
                :class="{'sortCol': sortMode=='dates'}"
                @click="setSortMode('dates')"
                v-html="`date<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>
            <th>
              <button
                :class="{'sortCol': sortMode=='types'}"
                @click="setSortMode('types')"
                v-html="`type<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>
            
            <!--
            <th>
              <button
                :class="{'sortCol': sortMode=='upvotes'}"
                @click="setSortMode('upvotes')"
                v-html="`upvotes<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>
            <th>
              <button
                :class="{'sortCol': sortMode=='downvotes'}"
                @click="setSortMode('downvotes')"
                v-html="`downvotes<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>
            <th>
              <button
                :class="{'sortCol': sortMode=='avgvotes'}"
                style="min-width: 120px;"
                @click="setSortMode('avgvotes')"
                v-html="`avg votes<br>${sortDir ? '&#8679;' : '&#8681;'}`"
              ></button>
            </th>


            <th>size</th>
            <th>date</th>
            <th>type</th>
            -->
            
            
            <th>delete</th>
          </tr>
          <tr v-for="idx in sortedArray">
            <td class="td">
              <div class="actualAsset" v-html="state.adminData.slugs[idx]"></div>
            </td>
            <td v-if="!state.showAssetPreview[idx]">
              <button @click="state.showAssetPreview[idx]=true">show</button>
            </td>
            <td v-else-if="state.adminData.filetypes[idx].indexOf('audio')!=-1" class="td"><a :href="state.URLbase + '/' + state.adminData.hrefs[idx]" target="_blank"><div :style="`background-image: url(${state.URLbase + '/musicNotes.svg'});`" class="avatar"></div></a></td>
            <td v-else-if="state.adminData.filetypes[idx].indexOf('image')!=-1" class="td"><a :href="state.URLbase + '/' + state.adminData.hrefs[idx]" target="_blank"><div :style="`background-image: url(${state.URLbase + '/' + state.adminData.hrefs[idx]});`" class="avatar"></div></a></td>
            <td v-else-if="state.adminData.filetypes[idx].indexOf('video')!=-1" class="td"><a :href="state.URLbase + '/' + state.adminData.hrefs[idx]" target="_blank"><video autoplay loop muted :src="state.adminData.hrefs[idx]" class="avatar"></video></a></td>
            <td>
              <button
                @click="state.toggleTrending(state.adminData.slugs[idx])"
                :style="`background: ${state.isTrending(state.adminData.slugs[idx]) ? '#f04':'#0f4'}`"
                v-html="state.isTrending(state.adminData.slugs[idx]) ? 'remove' : 'add'"
              >
              </button>
            </td>
            <td class="td">
              <div class="actualAsset" v-html="state.size(state.adminData.fileSizes[idx])"></div>
            </td>
            <td class="td">
              <div class="actualAsset" v-html="state.prettyDate({date: state.adminData.fileDates[idx]})"></div>
            </td>
            <td class="td">
              <div class="actualAsset" v-html="state.adminData.filetypes[idx]"></div>
            </td>
            <td class="td">
              <div
                @click="state.adminDeleteAsset(state.adminData.slugs[idx])"
                class="deleteSingleButton"
                title="nuke this asset and all its relations"
              ></div>
            </td>
          </tr>
        </table>
      </div>

      <div class="adminSection" v-if="state.adminData?.users">
        Users<br>
        <table>
          <tr>
            <th>id</th>
            <th>name</th>
            <th>avatar</th>
            <th>admin</th>
            <th>enabled</th>
            <th>assets</th>
            <th>joined</th>
            <th>seen</th>
          </tr>
          <tr v-for="(user, idx) in state.adminData.users">
            <td class="td"><div class="actualAsset" v-html="user.id"></div></td>
            <td class="td"><div class="actualAsset" v-html="user.name"></div></td>
            <td v-if="!state.showAvatarPreview[idx]">
              <button @click="state.showAvatarPreview[idx]=true">show</button>
            </td>
            <td v-else class="td"><div :style="`background-image: url(${state.avatar({userID: user.id})});`" class="avatar"></div></td>
            <td class="td"><div class="actualAsset" v-html="user.admin"></div></td>
            <td class="td"><div class="actualAsset" v-html="user.enabled"></div></td>
            <td class="td"><div class="actualAsset" v-html="user.slugs.length"></div></td>
            <td class="td"><div class="actualAsset" v-html="user.dateJoined"></div></td>
            <td class="td"><div class="actualAsset" v-html="user.dateSeen"></div></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <button
    v-if="!state.showPreview && !state.showModal"
    class="adminButton"
    @click="toggleShowAdmin()"
    v-html="state.showAdmin ? 'exit admin view' : 'show admin view'"
  >
  </button>
</template>

<script>

export default {
  name: 'Admin',
  props: [ 'state' ],
  components: {
  },
  data(){
    return {
      sortDir: true,
      sortMode: 'sizes',
      array: JSON.parse(JSON.stringify(this.state.adminData))
    }
  },
  methods: {
    close(){
      this.state.showAdmin = false
    },
    toggleShowAdmin(){
      if(!this.state.showAdmin){
        this.showLoading = true
        this.launch()
      }else{
        this.state.showAdmin = false
      }
    },
    launch(){
      this.state.getAdminData()
    },
    setSortMode(mode){
      switch(mode){
        case 'views':
          if(this.sortMode == 'views'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'views'
          }
        break
        case 'sizes':
          if(this.sortMode == 'sizes'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'sizes'
          }
        break
        case 'dates':
          if(this.sortMode == 'dates'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'dates'
          }
        break
        case 'types':
          if(this.sortMode == 'types'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'types'
          }
        break
        case 'upvotes':
          if(this.sortMode == 'upvotes'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'upvotes'
          }
        break
        case 'downvotes':
          if(this.sortMode == 'downvotes'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'downvotes'
          }
        break
        case 'avgvotes':
          if(this.sortMode == 'avgvotes'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'avgvotes'
          }
        break
      }
    }
  },
  computed: {
    ttlViews(){
      let ttl = 0
      this.array.map(v=>{
        ttl+=+v.views
      })
      return ttl
    },
    
    sortedByViews(){
      if(this.state.adminData){
        let ids = Array(this.state.adminData.fileViews.length).fill().map((v, idx) => {
          return {idx, views: this.state.adminData.fileViews[idx]}
        })
        ids.sort((a,b)=>b.views-a.views)
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByUpVotes(){
      let src = this.array == null ? [] : this.array
      return src.sort((a, b) => (this.sortDir?b:a).upvotes - (this.sortDir?a:b).upvotes)
    },
    sortedByDownVotes(){
      let src = this.array == null ? [] : this.array
      return src.sort((a, b) => (this.sortDir?b:a).downvotes - (this.sortDir?a:b).downvotes)
    },
    sortedByAvgVotes(){
      let src = this.array == null ? [] : this.array
      return src.sort((a, b) => ((this.sortDir?b:a).upvotes + (this.sortDir?b:a).downvotes) - ((this.sortDir?a:b).upvotes + (this.sortDir?a:b).downvotes))
    },
    sortedBySizes(){
      if(this.state.adminData){
        let ids = Array(this.state.adminData.fileSizes.length).fill().map((v, idx) => {
          return {idx, size: this.state.adminData.fileSizes[idx]}
        })
        ids.sort((a,b)=>b.size-a.size)
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByTypes(){
      if(this.state.adminData){
        let ids = Array(this.state.adminData.filetypes.length).fill().map((v, idx) => {
          return {idx, type: this.state.adminData.filetypes[idx]}
        })
        ids.sort((a,b)=>b.type-a.type)
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByDates(){
      let src = this.array == null ? [] : this.array
      return src.sort((a, b) => (new Date((this.sortDir?b:a).fileDates)) - (new Date((this.sortDir?a:b).fileDates)))
    },
    sortedArray(){
      switch(this.sortMode){
        case 'views'     : return this.sortedByViews; break
        case 'upvotes'   : return this.sortedByUpVotes; break
        case 'downvotes' : return this.sortedByDownVotes; break
        case 'avgvotes'  : return this.sortedByAvgViews; break
        case 'sizes'     : return this.sortedBySizes; break
        case 'types'     : return this.sortedByTypes; break
        case 'dates'     : return this.sortedByDates; break
      }
    },
    sortedBySizes(){
      if(this.state.adminData){
        let ids = Array(this.state.adminData.fileSizes.length).fill().map((v, idx) => {
          return {idx, size: this.state.adminData.fileSizes[idx]}
        })
        ids.sort((a,b)=>b.size-a.size)
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    }
  },
  mounted(){
  }
}
</script>

<style scoped>
.admin{
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  width: 100vw;
  height: 100vh;
  font-size: 14px;
}
.adminButton{
  position: fixed;
  bottom: 3px;
  z-index: 10000000;
  min-width: 240px;
  background: #f80;
  color: #000;
  right: 30px;
  border: 2px solid #f00;
}
.modalInner{
  background: #420c;
  overflow: auto;
}
.adminSection{
  box-sizing: border-box;
  margin: 40px;
  padding: 5px;
  background: #2228;
}
.actualAsset{
  width: 100%;
  font-size: 18px;
  text-align: center;
  border-radius: 5px;
  color: #fff;
  display: inline-block;
  padding-left: 0;
  padding-right: 5px;
}
table{
  border-collapse: collapse;
  display: inline-block;
}
td{
  text-align: center;
  border: 1px solid #4f88;
  padding: 3px;
  padding-left: 5px;
  padding-right: 5px;
}
th{
  font-size: .75em;
  padding-left: 5px;
  padding-right: 5px;
}
.avatar{
  width: 100px;
  height: 70px;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: contain;
  background-color: #000;
  border-radius: 5px;
  display: inline-block;
}
button{
  min-width: 80px;
  margin: 5px;
  line-height: 16px;
}
</style>
