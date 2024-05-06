<template>
  <div class="magnify">
    <div
      @click.stop.prevent="magnify()"
      class="magup"
    ></div>
    <div class="magLevel" v-html="magLevel"></div>
    <div
      @click.stop.prevent="unmagnify()"
      class="magdown"
    ></div>
    <div v-if="magLevel" class="magnifyingGlass" ref="magnifyingGlass">
    </div>
  </div>
</template>

<script>

export default {
  name: 'Magnify',
  props: [ 'state', 'element' ],
  components: {
  },
  data(){
    return {
      magLevel: 0,
      contents: null,
      maxMagLevel: 8
    }
  },
  methods: {
    magnify(){
      this.magLevel = Math.min(this.maxMagLevel, this.magLevel+1)
    },
    unmagnify(){
      this.magLevel = Math.max(0, this.magLevel-1)
    },
    refresh(){
      this.$refs.magnifyingGlass.style.left = this.mx-200 + 'px'
      this.$refs.magnifyingGlass.style.top = this.my-200 + 'px'
      this.contents.style.marginLeft = ((-this.mx+document.body.clientWidth/2+38)*this.magLevel-this.element.clientWidth/2) +'px'
      this.contents.style.marginTop = (-this.my*this.magLevel+this.element.clientHeight/2*this.magLevel+38*this.magLevel*2) + 'px'
    }
  },
  mounted(){
    this.contents = this.element.cloneNode(true)
    this.contents.style.transform = `scale(${this.magLevel})`
    this.$refs.magnifyingGlass.appendChild(this.contents)
  }
}
</script>

<style scoped>
  .magnify{
    position: absolute;
  }
  .magnifyingGlass{
    pointer-events: none;
    border-radius: 50%;
    border: 20px solid #fff2;
    width: 400px;
    height: 400px;
    positioN: absolute;
    overflow: clip;
    background: #000;
    cursor: crosshair;
  }
  .magup{
    background-image: ('./assets/mag.png');
  }
  .magdown{
    background-image: ('./assets/unmag.png');
  }
  .magLevel{
    border: 3px solid #40f8;
    background: #000c;
    padding: 5px;
    color: #fff;
    font-size: 20px;
    border-radius: 10px;
  }
  .contents{
    pointer-events: none;
  }
</style>
