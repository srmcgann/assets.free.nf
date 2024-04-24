<template>
  <div class="stats" ref="stats" tabindex="1000">
    <button
      v-if="!this.state.uploadInProgress"
      @click="close()"
      class="cancelButton"
      title="close this view [ESC]"
    >
      close/cancel
    </button>
    <div class="modalInner">
    teh statz
    asset count: {{state.userStats[state.loggedinUserID].length}}
    </div>
  </div>
</template>

<script>
export default {
  name: 'Stats',
  props: [ 'state' ],
  methods: {
    close(){
      this.state.showStats = false
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
    z-index: 1000;
    width: 100vw;
    height: 100vh;
    font-size: 14px;
  }
</style>
