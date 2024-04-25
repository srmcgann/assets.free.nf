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
      overview<br>
      <table class="statsTable">
        <tr>
          <td class="tdLeft">assets</td>
          <td class="tdRight" v-html="assets"></td>
        </tr>
      </table>
      
      <br><br>
      top views<br>
      <table class="statsTable">
        <tr>
          <th>asset</th>
          <th>thumb</th>
          <th>views</th>
          <th>size</th>
          <th>date</th>
          <th>type</th>
        </tr>
        <tr v-for="asset in sortedStats('views', viewsSortDirection)">
          <td class="td">
            <div class="actualAsset" v-html="asset.slug"></div>
          </td>
          <td v-if="!asset.showPreview">
            <button @click="asset.showPreview=true">show</button>
          </td>
          <td v-else-if="asset.filetype.indexOf('audio')!=-1" class="td"><a :href="state.URLbase + '/' + asset.href" target="_blank"><div :style="`background-image: url(${state.URLbase + '/musicNotes.svg'});`" class="avatar"></div></a></td>
          <td v-else-if="asset.filetype.indexOf('image')!=-1" class="td"><a :href="state.URLbase + '/' + asset.href" target="_blank"><div :style="`background-image: url(${state.URLbase + '/' + asset.href});`" class="avatar"></div></a></td>
          <td v-else-if="asset.filetype.indexOf('video')!=-1" class="td"><a :href="state.URLbase + '/' + asset.href" target="_blank"><video autoplay loop muted :src="asset.href" class="avatar"></video></a></td>
          <td class="tdRight" v-html="asset.views"></td>
          <td class="td">
            <div class="actualAsset" v-html="state.size(asset.size)"></div>
          </td>
          <td class="td">
            <div class="actualAsset" v-html="state.prettyDate(asset.date)"></div>
          </td>
          <td class="td">
            <div class="actualAsset" v-html="asset.filetype"></div>
          </td>
        </tr>
      </table>
      
      <br><br>
      top votes<br>
      <table class="statsTable">
        <tr>
          <th>asset</th>
          <th>upvotes</th>
          <th>downvotes</th>
          <th>avg</th>
        </tr>
        <tr v-for="asset in sortedStats('votes', votesSortDirection)">
          <td class="tdRight" v-html="asset.slug"></td>
          <td class="tdRight" v-html="asset.upvotes"></td>
          <td class="tdRight" v-html="asset.downvotes"></td>
          <td class="tdRight" v-html="(asset.upvotes+asset.downvotes)/2"></td>
        </tr>
      </table>
      
      <br><br>
      footprint<br>
      <table class="statsTable">
        <tr>
          <th>asset</th>
          <th>size</th>
        </tr>
        <tr v-for="asset in sortedStats('sizes', sizesSortDirection)">
          <td class="tdRight" v-html="asset.slug"></td>
          <td class="tdRight" v-html="asset.size"></td>
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
      viewsSortDirection: true,
      sizesSortDirection: true,
      votesSortDirection: true,
      assetsArray: []
    }
  },
  methods: {
    close(){
      this.state.showStats = false
    },
    analyze(){
    },
    sortedStats(mode, dir){
      let src = this.assetsArray
      switch(mode){
        case 'views': return src.sort((a, b) => (dir?b:a).views - (dir?a:b).views); break
        case 'votes': return src.sort((a, b) => ((dir?b:a).upvotes + (dir?b:a).downvotes) - ((dir?a:b).upvotes + (dir?a:b).downvotes)); break
        case 'sizes': return src.sort((a, b) => (dir?b:a).size - (dir?a:b).size); break
      }
    }
  },
  computed: {
    assets(){
      return this.state.userStats[this.state.loggedinUserID].length
    }
  },
  mounted(){
    this.assetsArray = JSON.parse(JSON.stringify(this.state.userStats[this.state.loggedinUserID]))
    this.$refs.stats.focus()
    this.analyze()
  }
}
</script>

<style scoped>
  .stats{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
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
  th{
    padding-left: 20px;
    padding-right: 20px;
  }
  .tdLeft, .tdRight{
    text-align: center;
  }
</style>
