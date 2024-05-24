<template>
  <div class="magnify">
    <div
      class="magLevel"
      v-html="state.magLevel"
      title="current magnification"
    ></div><br>
    <div
      @click.stop.prevent="magnify()"
      class="magup specialToolButton"
      title="increase magnification [+]"
    ></div><br>
    <div
      @click.stop.prevent="unmagnify()"
      class="magdown specialToolButton"
      title="decrease magnification [-]"
    ></div><br>
    <div
      @click.stop.prevent="cancelMagnify()"
      class="nomag specialToolButton"
      title="cancel magnification [0]"
    ></div>
    <div v-if="state.magLevel" class="magnifyingGlass" ref="magnifyingGlass"></div>
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
      contents: null,
    }
  },
  methods: {
    cancelMagnify(){
      do{
        this.unmagnify()
      }while(this.state.magLevel)
    },
    magnify(){
      this.state.magLevel = Math.min(this.state.maxMagLevel, this.state.magLevel+1)
    },
    unmagnify(){
      this.state.magLevel = Math.max(0, this.state.magLevel-1)
    },
    reset(){
      if(typeof this.$refs?.magnifyingGlass !== 'undefined'){
        if(this.$refs.magnifyingGlass.contains(this.contents)){
          this.$refs.magnifyingGlass.removeChild(this.contents)
          this.contents = this.element.cloneNode(true)
          this.contents.style.width = this.element.clientWidth + 'px'
          this.contents.className = 'contents'
          this.contents.style.transform = `scale(${this.state.magLevel+1})`
        }
        this.$nextTick(() => {
          this.$refs.magnifyingGlass.appendChild(this.contents)

          if(this.state.previewLink.filetype.split('/')[0] == 'video'){
            console.log('video link detected')
            let tel = document.querySelectorAll('.previewAsset')
            if(tel.length>1) {
              console.log('playing asset')
              tel=tel[0]
              tel.muted = true
              tel.loop = true
              tel.play()
            }
          }

          this.refresh()
        })
      }
    },
    refresh(){
      if(this.$refs?.magnifyingGlass != null &&
         !this.$refs.magnifyingGlass.contains(this.contents)) {
        this.$nextTick(()=>this.reset())
      }else{
        if(
          typeof this.$refs.magnifyingGlass != 'undefined' &&
          this.$refs.magnifyingGlass !== null){
          this.$refs.magnifyingGlass.style.display = this.pause ? 'none' : 'block'
          if(this.state.magLevel){
            this.$refs.magnifyingGlass.style.left = this.state.mx-400 + 'px'
            this.$refs.magnifyingGlass.style.top = this.state.my-400 + 'px'
            this.contents.style.marginLeft = (-document.body.clientWidth/2 - this.state.mx + document.body.clientWidth/2-100) * (this.state.magLevel+1) + 440 + 800*((this.state.magLevel+1)-1) + 'px'
            this.contents.style.marginTop = ((this.element.clientHeight/2+(-this.state.my-this.element.clientHeight/2)) * (this.state.magLevel+1) + 400) + 'px'
          }
        }
      }
    }
  },
  mounted(){
    this.contents = this.element.cloneNode(true)
    this.contents.style.width = this.element.clientWidth + 'px'
    this.contents.className = 'contents'
    this.contents.style.transform = `scale(${this.state.magLevel+1})`
    window.addEventListener('mousemove', e => {
      this.state.mx = e.pageX
      this.state.my = e.pageY
      if(this.state.magLevel) this.refresh()
    })
    if(this.state.magLevel){
      this.$nextTick(()=>{
        this.refresh()
      })
    }
  },
  beforeUnmount(){
    window.removeEventListener('mousemove', e => {
      this.state.mx = e.pageX
      this.state.my = e.pageY
      if(this.state.magLevel) this.refresh()
    })
  },
  watch:{
    'state.keys[48]'(val){
      if(val){
        this.cancelMagnify()
      }
    },
    'state.keys[96]'(val){
      if(val){
        this.cancelMagnify()
      }
    },
    'state.magLevel'(val){
      if(val){
        this.contents.style.transform = `scale(${this.state.magLevel+1})`
        this.refresh()
        this.$nextTick(()=>{
          this.refresh()
        })
      }
    }
  }
}
</script>

<style scoped>
  .magnify{
    position: absolute;
    right: 10px;
    bottom: 0px;
    z-index: 10000;
    opacity: 1;
  }
  .magnifyingGlass{
    pointer-events: none;
    border-radius: 50%;
    border: 2px solid #8883;
    box-shadow: 0 0 40px 40px #fff4;
    box-sizing: unset;
    width: 800px;
    height: 800px;
    position: fixed;
    overflow: clip;
    background: #000;
    cursor: crosshair;
  }
  .magup, .magdown, .nomag{
    background-size: 32px;
  }
  .magup{
    background-image: url(../assets/mag.png);
  }
  .magdown{
    background-image: url(../assets/unmag.png);
  }
  .nomag{
    background-image: url(../assets/nomag.png);
  }
  .magLevel{
    text-align: center;
    border: 3px solid #40f8;
    background: #000c;
    padding: 5px;
    color: #fff;
    font-size: 32px;
    border-radius: 50%;
    line-height: 26px;
    width: 35px;
    height: 35px;
    position: absolute;
    right: 6px;
    margin-top: -26px;
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