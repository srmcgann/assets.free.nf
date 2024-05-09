<template>
  <label
    @mouseover="pauseMag=true"
    @mouseout="pauseMag=false"
    class="checkboxLabel"
    title="pinning prevents info from fading out..."
    style="z-index: 100000; margin: 10px; margin-left: 40px;"
  >
    <input type="checkbox" v-model="state.pinned" @input="togglePinned()">
    <span class="checkmark" style="margin-left:-36px;width:25px;margin-top:-6px;"></span>
    <span style="margin-top:-11px;width:40px;margin-left:-56px;width:40px;position: absolute;padding-left: 10px">📌</span>
  </label>
  <button
    @click="close()"
    @mouseover="pauseMag=true"
    @mouseout="pauseMag=false"
    class="cancelButton"
    title="close this view"
   >
    close/cancel
  </button>
  <Magnify
    v-if="mounted"
    @mouseover="pauseMag=true"
    @mouseout="pauseMag=false"
    :class="{'fade': !state.pinned && !state.magLevel}"
    :state="state" :pause="pauseMag"
    :element="previewContainer"
  />
  <div
    ref="previewContainer"
    class="previewContainer"
  >
    <div
      class="preview"
      @mousemove="bumpNavButtonOpacity()"
      ref="preview"
    >
      <div class="previewInner">
        <div class="slideshow" ref="slideshow"></div>
      </div>
    </div>
  </div>
  <div
    class="inputs"
    ref="inputs"
    :class="{'fade': !state.pinned}"
  >
    <div
      @mouseover="pauseMag=true"
      @mouseout="pauseMag=false"
      @mousemove="state.bumpADOpacity++"
      class="linkButtons"
    >
      <div
        class="specialToolButton"
        @click.prevent.stop="state.setLinkProperty(link, 'private', link.private?0:1)"
        :class="{'private': link.private, 'notPrivate': !link.private}"
        :title="`toggle visibility. (currently: ${link.private?'NOT':''} featured in public galleries)`"
        v-if="link.userID == state.loggedinUserID || state.admin"
      ></div>
      <div
        class="copyLinkButton"
        @click.prevent.stop="state.copyLink(link.href)"
        title="copy link to clipboard"
      ></div>
      <a
        :href="state.URLbase + '/' + link.href"
        class="openButton"
        @click.prevent.stop="state.openLink(link)"
        title="open link in new tab"
      ></a>
      <div
        class="downloadButton"
        @click.prevent.stop="state.downloadLink(link, state.fullFileName(link))"
        title="download asset"
      ></div>
      <div
        class="deleteSingleButton"
        @click.prevent.stop="state.deleteSingle(link)"
        title="delete this asset only"
        v-if="link.userID == state.loggedinUserID || state.admin"
      ></div>
    </div>
    <AssetData
      :state="state"
      :link="link"
      :omitAssetData="false"
      @mouseover="pauseMag=true"
      @mouseout="pauseMag=false"
      @mousemove="bumpNavButtonOpacity()"
    />
    <div
      v-if="state.multipleLinks()"
      class="leftButton"
      ref = "leftButton"
      @mouseover="pauseMag=true;bumpNavButtonOpacity(true)"
      @mouseout="pauseMag=false"
      @click="state.prev()"
      @mousemove="bumpNavButtonOpacity()"
      title="view previous asset [left arrow]"
    ></div>
    <div
      v-if="state.multipleLinks()"
      class="rightButton"
      ref = "rightButton"
      @mouseover="pauseMag=true;bumpNavButtonOpacity(true)"
      @mouseout="pauseMag=false"
      @click="state.next()"
      @mousemove="bumpNavButtonOpacity()"
      title="view next asset [right arrow]"
    ></div>
  </div>
</template>

<script>
import AssetData from './AssetData'
import Magnify from './Magnify'

export default {
  name: 'Preview',
  components: {
    AssetData,
    Magnify
  },
  props: [ 'state', 'link' ],
  data(){
    return {
      asset: null,
      linkType: '',
      mounted: false,
      pauseMag: false
    }
  },
  computed:{
    previewContainer(){
      //let el = document.querySelectorAll('.previewAsset')
      //if(el.length) return el[0]
      return this.$refs.previewContainer
    }
  },
  methods: {
    togglePinned(){
      this.state.pinned = !this.state.pinned
      this.$nextTick(()=>{
        this.bumpNavButtonOpacity()
      })
    },
    bumpNavButtonOpacity(force=false){
      if(!force && !this.state.pinned && this.state.magLevel) return
      this.$refs.inputs.classList.remove('fade')
      this.$refs.inputs.style.height = this.$refs.inputs.clientHeight + 'px'
      this.$refs.inputs.classList.add('fade')
      if(this.state.blockFade || this.state.pinned) {
        this.$refs.inputs.classList.add('fullOpacity')
      }else{
        this.$refs.inputs.classList.remove('fullOpacity')
      }
    },
    close(){
      this.state.closePreview()
    }
  },
  watch:{
    'state.bumpADOpacity'(val){
      if(val) {
        this.bumpNavButtonOpacity()
        this.state.bumpADOpacity = 0
      }
    }
  },
  mounted(){
    this.linkType = this.link.filetype.split('/')[0]

    if(this.linkType == 'image' || this.linkType == 'audio'){
      this.asset = document.createElement('div')
      this.asset.className = 'previewAsset'
      this.asset.style.top = '50%'
      this.asset.style.left = '50%'
      this.asset.style.width = '100%'
      this.asset.style.height = '100%'
      this.asset.style.position = 'absolute'
      this.asset.style.transform = 'translate(-50%, -50%)'
      this.asset.style.objectFit = 'contain'
      this.asset.style.backgroundColor = '#000'
      this.asset.style.backgroundSize = 'contain'
      this.asset.style.backgroundRepeat = 'no-repeat'
      this.asset.style.backgroundPosition = 'center center'
      let loadingItem = new Image()
      this.state.showLoading = true
      loadingItem.onload = () => {
        this.state.showLoading = false
      }
      let res = this.linkType == 'audio' ? 'musicNotes.svg' : this.state.URLbase + '/' + this.link.href
      loadingItem.src = res
      this.asset.style.backgroundImage = `url(${res})`
      this.$refs.slideshow.appendChild(this.asset)
    }
    if(this.linkType == 'audio'){
      this.asset = document.createElement('audio')
      this.asset.className = 'previewAsset'
      this.asset.controls = true
      this.asset.style.position = 'absolute'
      this.asset.style.left = '50%'
      this.asset.style.top = '50%'
      this.asset.loop = true
      this.asset.style.transform = 'translate(-50%, -50%)'
      this.asset.oncanplay = () => {
        this.asset.play()
        this.state.showLoading = false
      }
      this.$refs.slideshow.appendChild(this.asset)
      this.asset.src = this.state.URLbase + '/' + this.link.href
    }
    if(this.linkType == 'video'){
      this.asset = document.createElement('video')
      this.asset.className = 'previewAsset'
      this.asset.controls = true
      this.asset.style.position = 'absolute'
      this.asset.style.left = '50%'
      this.asset.style.top = '50%'
      this.asset.style.width = '100%'
      this.asset.style.height = '100%'
      this.asset.style.objectFit = 'contain'
      this.asset.style.background = '#000'
      this.asset.loop = true
      this.asset.muted = !this.state.userInteraction
      this.asset.style.transform = 'translate(-50%, -50%)'
      this.asset.oncanplay = () => {
        this.asset.play()
        this.state.showLoading = false
      }
      this.$refs.slideshow.appendChild(this.asset)
      this.asset.src = this.state.URLbase + '/' + this.link.href
    }
    this.mounted = true
  },
  beforeUnmount(){
    if(typeof this.asset?.paused == 'boolean') this.asset.paused = true
    this.asset.src = ''
  }
}
</script>
<style scoped>
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
    animation: fadeOut 3s 1 linear;
  }
  .leftButton{
    background-image: url(../assets/leftButton.png);
    left: 10px;
  }
  .rightButton{
    background-image: url(../assets/rightButton.png);
    right: 10px;
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
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    margin: 0;
    z-index: 1000;
  }
</style>