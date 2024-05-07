<template>
  <div class="magnify">
    <div
      @click.stop.prevent="magnify()"
      class="magup specialToolButton"
    ></div>
    <div class="magLevel" v-html="magLevel"></div>
    <div
      @click.stop.prevent="unmagnify()"
      class="magdown specialToolButton"
    ></div>
    <div v-if="magLevel" class="magnifyingGlass" ref="magnifyingGlass"></div>
  </div>
</template>

<script>

export default {
  name: 'Magnify',
  props: [ 'state', 'element', 'pause'],
  components: {
  },
  data(){
    return {
      magLevel: 0,
      contents: null,
      maxMagLevel: 8,
      appended: false,
    }
  },
  methods: {
    magnify(){
      this.magLevel = Math.min(this.maxMagLevel, this.magLevel+1)
      this.contents.style.transform = `scale(${this.magLevel})`
      if(this.magLevel == 1) {
        this.appended = true
        this.$nextTick(() => {
          this.$refs.magnifyingGlass.appendChild(this.contents)
        })
      }
    },
    unmagnify(){
      this.magLevel = Math.max(0, this.magLevel-1)
      this.contents.style.transform = `scale(${this.magLevel})`
    },
    refresh(e){
      if(
        typeof this.$refs.magnifyingGlass != 'undefined' &&
        typeof this.$refs.magnifyingGlass != 'null'){
        this.$refs.magnifyingGlass.style.display = this.pause ? 'none' : 'block'
        if(this.magLevel){
          this.mx = e.pageX
          this.my = e.pageY
          this.$refs.magnifyingGlass.style.left = this.mx-200 + 'px'
          this.$refs.magnifyingGlass.style.top = this.my-200 + 'px'
          this.contents.style.marginLeft = ((this.element.clientWidth/2+(-this.mx-this.element.clientWidth/2)+100) * this.magLevel + 200) + 'px'
          this.contents.style.marginTop = ((this.element.clientHeight/2+(-this.my-this.element.clientHeight/2)) * this.magLevel + 200) + 'px'
        }
      }
    }
  },
  mounted(){
    this.contents = this.element.cloneNode(true)
    this.contents.style.width = this.element.clientWidth + 'px'
    this.contents.className = 'contents'
    this.contents.style.transform = `scale(${this.magLevel})`
    window.addEventListener('mousemove', e => this.refresh(e))
  },
  beforeUnmount(){
    window.removeEventListener('mousemove', e => this.refresh(e))
  }
}
</script>

<style scoped>
  .magnify{
    position: absolute;
    right: 10px;
    bottom: 0px;
    z-index: 100;
    opacity: .75;
  }
  .magnifyingGlass{
    pointer-events: none;
    border-radius: 50%;
    border: none;
    width: 400px;
    height: 400px;
    position: fixed;
    overflow: clip;
    background: #000;
    cursor: crosshair;
  }
  .magup, .magdown{
    background-size: 32px;
  }
  .magup{
    background-image: url(../assets/mag.png);
  }
  .magdown{
    background-image: url(../assets/unmag.png);
  }
  .magLevel{
    text-align: center;
    border: 3px solid #40f8;
    background: #000c;
    padding: 5px;
    color: #fff;
    font-size: 20px;
    border-radius: 10px;
    line-height: 8.5px;
    height: 20px;
  }
  
  .contents{
    pointer-events: none;
    display: inline-block;
  }


  .preview{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 100vh;
    font-size: 14px;
  }
  .slideshow{
    margin: 100px;
    height: 100%;
    width: 100%;
    border: 1px solid red;
  }
  .leftButton, .rightButton{
    width: 100px;
    height: 100px;
    position: fixed;
    top: 50%;
    transform: translate(0, -50%);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: contain;
    cursor: pointer;
  }
  @keyframes fadeOut{
    0% {
      opacity: 1;
    }
    66% {
      opacity: 1;
    }
    100% {
      opacity: 0;
    }
  }
  .fade{
    /* animation: fadeOut 3s 1 linear; */
  }
  .leftButton{
    background-image: url(../assets/leftButton.png);
    left: 10px;
    display: none;
  }
  .rightButton{
    background-image: url(../assets/rightButton.png);
    right: 10px;
    display: none;
  }
  .fullOpacity{
    opacity: 1!important;
  }
  .inputs{
    opacity: 0;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 20000;
  }
  .previewInner{
    text-align: center;
    padding: 25px;
    width: 100%;
    height: 100%;
    font-size: 25px;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
    background: #001b;
  }
  .linkButtons{
    margin-top: 11px;
    display: inline-block;
    right: 16px;
    position: absolute;
    width: 44px;
    z-index: 10;
    top: 40px;
    display: none;
  }
  .assetData{
    position: absolute;
    top: 0;
    left: 50%;
    transform: translate(-50%);
    background: #000a;
    max-width: 500px;
  }
  .previewContainer{
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    margin: 0;
  }
</style>