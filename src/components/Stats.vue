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
    <div class="modalInner">
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
          <th>views</th>
        </tr>
        <tr v-for="asset in sortedStats('views', viewsSortDirection)">
          <td class="tdRight" v-html="asset.slug"></td>
          <td class="tdRight" v-html="asset.views"></td>
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
        <tr v-for="asset in sortedStats('votes', viewsSortDirection)">
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
        <tr v-for="asset in sortedStats('sizes', viewsSortDirection)">
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
      viewsSortDirection: false,
      sizesSortDirection: false,
      votesSortDirection: false,
    }
  }
  methods: {
    close(){
      this.state.showStats = false
    },
    analyze(){
      
    }
  },
  computed:{
    assets(){
      return state.userStats[state.loggedinUserID].length
    },
    sortedStats(mode, dir){
      let src = JSON.parse(JSON.stringify(state.userStats[state.loggedinUserID]))
      switch(mode){
        case 'views': return src.sort((a, b) => (dir?b:a).views - (dir?a:b).views); break
        case 'votes': return src.sort((a, b) => ((dir?b:a).upvotes + (dir?b:a).downvotes) - ((dir?a:b).upvotes + (dir?a:b).downvotes)); break
        case 'sizes': return src.sort((a, b) => (dir?b:a).size - (dir?a:b).size); break
      }
    }
  },
  mounted(){
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
    width: 100%;
  }
</style>
