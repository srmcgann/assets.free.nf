<template>
  <div class="loading">
    <video class="loadingVid" autoplay muted loop :src="`${state.URLbase}/loading.mp4`"></video>
  </div>
</template>

<script>

export default {
  name: 'Loading',
  props: [ 'state' ],
  data(){
    return {
    }
  },
  methods: {
  },
  mounted(){
  }
}
</script>

<style scoped>
  .loading{
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    box-sizing: border-box;
    padding: 100px;
    z-index: 10000;
  }
  .loadingVid{
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: inherit;
    height: inherit;
    opacity: .3
  }
</style>
