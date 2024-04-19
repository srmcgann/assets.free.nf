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
            <th>size</th>
            <th>type</th>
          </tr>
          <tr v-for="idx in sortedBySizes">
            <td class="td">
              <div class="actualAsset" v-html="state.adminData.slugs[idx]"></div>
            </td>
            <td v-if="!state.showAssetPreview[idx]">
              <button @click="state.showAssetPreview[idx]=true">show</button>
            </td>
            <td v-else-if="state.adminData.fileTypes[idx].indexOf('audio')!=-1" class="td"><a :href="state.adminData.hrefs[idx]" target="_blank"><div :style="`background-image: url(${state.URLbase + '/musicNotes.svg'});`" class="avatar"></div></a></td>
            <td v-else-if="state.adminData.fileTypes[idx].indexOf('image')!=-1" class="td"><a :href="state.adminData.hrefs[idx]" target="_blank"><div :style="`background-image: url(${state.adminData.hrefs[idx]});`" class="avatar"></div></a></td>
            <td v-else-if="state.adminData.fileTypes[idx].indexOf('video')!=-1" class="td"><a :href="state.adminData.hrefs[idx]" target="_blank"><video autoplay loop muted :src="state.adminData.hrefs[idx]" class="avatar"></video></a></td>
            <td class="td">
              <div class="actualAsset" v-html="state.size(state.adminData.fileSizes[idx])"></div>
            </td>
            <td class="td">
              <div class="actualAsset" v-html="state.adminData.fileTypes[idx]"></div>
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
            <td v-else class="td"><div :style="`background-image: url(${user.avatar});`" class="avatar"></div></td>
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
    @click="toggleShowAdmin"
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
    }
  },
  computed: {
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
  methods: {
    toggleShowAdmin(){
      this.state.showAdmin = !this.state.showAdmin
      if(this.state.showAdmin) this.launch()
    },
    launch(){
      this.state.getAdminData()
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
</style>
