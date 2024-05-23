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
      &lt;&lt;&lt; STATS &gt;&gt;&gt;<br><br>
      <div
        class="avatar"
        style="width:320px; height: 180px;"
        :title="`user: ${state.userInfo[state.userStatsID]?.name}`"
        :style="`background-image: url(${state.avatar({userID: state.userStatsID})})`"
      ></div>
      <br>
      <button
        class="commentButton"
        @click="state.openUserPage(state.userStatsID)"
        style="padding: 5px;color:#000;font-size:20px;background:#ff0;"
        v-html="state.shortText(state.userInfo[state.userStatsID]?.name, 16)"
        :title="`view ${state.shortText(state.userInfo[state.userStatsID]?.name, 16)}'s assets`"
      ></button>
      <hr>
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
      <hr>
      <br>
      <table class="statsTable">
        <tr>
          <th>asset</th>
          <th>thumb</th>
          <th>
            <button
              :class="{'sortCol': sortMode=='views'}"
              @click="setSortMode('views')"
              v-html="`views<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='upvotes'}"
              @click="setSortMode('upvotes')"
              v-html="`upvotes<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='votesCast'}"
              @click="setSortMode('votesCast')"
              v-html="`votesCast<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='popularity'}"
              @click="setSortMode('popularity')"
              v-html="`pop<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
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
        </tr>
        <tr v-for="link in sortedArray">
          <td class="tdRight">
            <div class="actualAsset" v-html="link.slug"></div>
          </td>
          <td v-if="!link.showPreview">
            <button @click="link.showPreview=true">show</button>
          </td>
          <td v-else-if="link.filetype.indexOf('audio')!=-1" class="tdRight"><a :href="state.URLbase + '/' + link.href" target="_blank"><div :style="`background-image: url(${state.URLbase + '/musicNotes.svg'});`" class="avatar"></div></a></td>
          <td v-else-if="link.filetype.indexOf('image')!=-1" class="tdRight"><a :href="state.URLbase + '/' + link.href" target="_blank"><div :style="`background-image: url(${state.URLbase + '/' + link.href});`" class="avatar"></div></a></td>
          <td v-else-if="link.filetype.indexOf('video')!=-1" class="tdRight"><a :href="state.URLbase + '/' + link.href" target="_blank"><video autoplay loop muted :src="state.URLbase + '/' + link.href" class="avatar"></video></a></td>
          <td class="tdRight" v-html="link.views"></td>
          <td class="tdRight">
            <div class="actualAsset" v-html="link.upvotes"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="link.votesCast"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="`${state.voteRatingPerc(link)*100}%`"></div>
          </td>
          <td class="tdRight" style="min-width: 100px;">
            <div class="actualAsset" v-html="state.size(link.size)"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="state.prettyDate({date: link.date})"></div>
          </td>
          <td class="tdRight">
            <div class="actualAsset" v-html="link.filetype"></div>
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
        case 'votesCast':
          if(this.sortMode == 'votesCast'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'votesCast'
          }
        break
        case 'popularity':
          if(this.sortMode == 'popularity'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'popularity'
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
    sortedByVotesCast(){
      let src = this.array
      return src.sort((a, b) => (this.sortDir?b:a).votesCast - (this.sortDir?a:b).votesCast)
    },
    sortedByPopularity(){
      let src = this.array
      return src.sort((a, b) => this.state.voteRatingPerc(this.sortDir?b:a) - this.state.voteRatingPerc(this.sortDir?a:b))
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
        case 'views'      : return this.sortedByViews; break
        case 'upvotes'    : return this.sortedByUpVotes; break
        case 'votesCast'  : return this.sortedByVotesCast; break
        case 'popularity' : return this.sortedByPopularity; break
        case 'sizes'      : return this.sortedBySizes; break
        case 'types'      : return this.sortedByTypes; break
        case 'dates'      : return this.sortedByDates; break
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
    background: #000;
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
  .tdLeft{
    text-align: right;
  }
  .tdRight{
    text-align: left;
  }
  .tdLeft, .tdRight{
    max-width: unset;
    padding: 5px;
    font-size: 1.2em;
  }
  hr{
    border: 1px solid #0f0;
  }
  .avatar{
    width: 160px;
    height: 90px;
    display: inline-block;
  }
</style>
