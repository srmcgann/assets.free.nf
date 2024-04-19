<template>
  <div class="header">
    <div class="headerImg" @click="reload()"></div>
    <User :state="state" v-if="!state.loggedIn"/>
  </div>
</template>

<script>
import User from './User'

export default {
  name: 'Header',
  props: [ 'state' ],
  components: {
    User
  },
  data(){
    return {
    }
  },
  methods: {
    reload(){
      window.location.reload()
    }
  },
  mounted(){
  }
}
</script>

<style scoped>
  .header{
    height: 52px;
    background: linear-gradient(90deg, #000, #028d, #103d);
    color: #4fa;
    font-size: 20px;
    position: absolute;
    top: 0;
    left: 0;
    width: calc(100vw - 20px);
    z-index: 100;
    min-width: 720px;
  }
  .headerImg{
    cursor: pointer;
    height: 52px;
    width: 390px;
    background-position: 0 50%;
    background-image: url(../assets/header.png);
    background-repeat: no-repeat;
    background-size: auto 52px;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
  }
</style>
