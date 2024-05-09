<template>
  <button
    v-if="!this.state.uploadInProgress"
    @click="close()"
    class="cancelButton"
    title="close this view [ESC]"
  >
    close/cancel
  </button>
  <div class="stats" ref="stats" tabindex="1000">
    <div class="modalInner" style="overflow: auto;">
      overview of<br>
      <div
        class="avatar"
        :title="`user: ${state.userInfo[state.userStatsID]?.name}`"
        :style="`background-image: url(${state.avatar({userID: state.userStatsID})`"
      ></div>
      <br><b>{{state.userInfo[state.userStatsID].name}}'s</b> assets<br>
      <table class="statsTable">
        <tr>
          <td
            class="tdLeft"
            style="font-size:2em;"
          >assets</td>
          <td
            class="tdRight"
            style="font-size:2em;"
            v-html="assets"
          ></td>
        </tr>
        <tr>
          <td
            class="tdLeft"
            style="font-size:2em;"
          >ttl views</td>
          <td
            class="tdRight"
            style="font-size:2em;"
            v-html="ttlViews"
          ></td>
        </tr>
        <tr>
          <td
            class="tdLeft"
            style="font-size:2em;"
          >footprint</td>
          <td
            class="tdRight"
            style="font-size:2em;"
            v-html="footprint"
          ></td>
        </tr>
      </table>
      <br><br>
      <table class="statsTable">
        <tr>
          <th>asset</th>
          <th>thumb</th>
          <th>
            <button
              :class="{'sortCol': sortMode=='views'}"
              @click="setSortMode('views')"
              v-html="`views<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='sizes'}"
              @click="setSortMode('sizes')"
              v-html="`size<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='dates'}"
              @click="setSortMode('dates')"
              v-html="`date<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='types'}"
              @click="setSortMode('types')"
              v-html="`type<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='upvotes'}"
              @click="setSortMode('upvotes')"
              v-html="`upvotes<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='downvotes'}"
              @click="setSortMode('downvotes')"
              v-html="`downvotes<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='avgvotes'}"
              style="min-width: 120px;"
              @click="setSortMode('avgvotes')"
              v-html="`avg votes<br>[${sortDir ? '&#8679;' : '&#8681;'}]`"
            ></button>
          </th>
        </tr>
        <tr v-for="asset in sortedArray">
          <td class="tdRight">
            <div class="actualAsset" v-html="asset.slug"></div>
          </td>
          <td v-if="!asset.showPreview">
            <button @click="asset.showPreview=true">show</button>
          </td>
          <td v-else-if="asset.filetype.indexOf('audio')!=-1" class="tdRight"><a :href="state.URLbase + '/' + asset.href" target="_blank"><div :style="`background-image: url(${state.URLbase + '/musicNotes.svg'});`" class="avatar"></div></a></td>
          <td v-else-if="asset.filetype.indexOf('image')!=-1" class="tdRight"><a :href="state.URLbase + '/' + asset.href" target="_blank"><div :style="`background-image: url(${state.URLbase + '/' + asset.href});`" class="avatar"></div></a></td>
          <td v-else-if="asset.filetype.indexOf('video')!=-1" class="tdRight"><a :href="state.URLbase + '/' + asset.href" target="_blank"><video autoplay loop muted :src="state.URLbase + '/' + asset.href" class="avatar"></video></a></td>
          <td class="tdRight" v-html="asset.views"></td>
          <td class="tdRight">
            <div class="actualAsset" v-html="state.size(asset.size)"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="state.prettyDate({date: asset.date})"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="asset.filetype"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="asset.upvotes"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="asset.downvotes"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="(asset.upvotes + asset.downvotes)/2"></div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Stats',
  props: [ 'state' ],
  data(){
    return {
      sortDir: true,
      sortMode: 'views',
      array: JSON.parse(JSON.stringify(this.state.userStats[this.state.userStatsID]))
    }
  },
  methods: {
    close(){
      this.state.showStats = false
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
      let src = this.array
      return src.sort((a, b) => (this.sortDir?b:a).views - (this.sortDir?a:b).views)
    },
    sortedByUpVotes(){
      let src = this.array
      return src.sort((a, b) => (this.sortDir?b:a).upvotes - (this.sortDir?a:b).upvotes)
    },
    sortedByDownVotes(){
      let src = this.array
      return src.sort((a, b) => (this.sortDir?b:a).downvotes - (this.sortDir?a:b).downvotes)
    },
    sortedByAvgVotes(){
      let src = this.array
      return src.sort((a, b) => ((this.sortDir?b:a).upvotes + (this.sortDir?b:a).downvotes) - ((this.sortDir?a:b).upvotes + (this.sortDir?a:b).downvotes))
    },
    sortedBySizes(){
      let src = this.array
      return src.sort((a, b) => (this.sortDir?b:a).size - (this.sortDir?a:b).size)
    },
    sortedByTypes(){
      let src = this.array
      return src.sort((a, b) => (this.sortDir?b:a).filetype - (this.sortDir?a:b).filetype)
    },
    sortedByDates(){
      let src = this.array
      return src.sort((a, b) => (new Date((this.sortDir?b:a).date)) - (new Date((this.sortDir?a:b).date)))
    },
    assets(){
      return this.state.userStats[this.state.userStatsID].length
    },
    footprint(){
      let ret = 0
      this.state.userStats[this.state.userStatsID].map(v=>{
        ret += v.size
      })
      return this.state.size(ret)
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
    }
  },
  mounted(){
    this.$refs.stats.focus()
  }
}
</script>

<style scoped>
  .stats{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100000;
    width: 100vw;
    height: 100vh;
    font-size: 14px;
  }
  .statsTable{
    border-collapse: collapse;
    font-size: 14px;
    text-shadow: 2px 2px 2px #000;
    background: #0003;
    display: inline-block;
    width: unset;
  }
  .modalInner{
    height: 100vh;
    padding-bottom: 200px;
  }
  .cancelButton{
    background: #822;
    color: #f88;
  }
  button{
    background: #40f;
    color: #fff;
    min-width: unset;
    text-shadow: 1px 1px 3px #40f;
  }
  .sortCol{
    background: #2fc;
    color: #000;
  }
  th{
    padding-left: 10px;
    padding-right: 10px;
  }
  .tdLeft, .tdRight{
    max-width: unset;
    padding: 5px;
    text-align: center;
    font-size: 1.2em;
  }
  .avatar{
    width: 160px;
    height: 90px;
  }
</style>
