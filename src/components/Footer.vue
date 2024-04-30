<template>
  <div class="footer">
    <span v-html="state.footerMsg"></span>
    <Pages :state="state" v-if="state.mode!='col'" />
  </div>
</template>

<script>
import Pages from './Pages'

export default {
  name: 'Footer',
  props: [ 'state' ],
  components: {
    Pages
  },
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
  .footer{
    height: 70px;
    background: linear-gradient(90deg, #206, #206, #206a, #0000, #000a, #000, #000);
    color: #fff;
    width: calc(100vw - 20px);
    left: 0;
    font-size: 20px;
    text-shadow: 2px 2px 2px #000;
    padding-top: 5px;
    line-height: 16px;
    box-sizing: border-box;
    text-align: left;
    padding-left: 5px;
    position: fixed;
    bottom: 0;
  }
</style>
