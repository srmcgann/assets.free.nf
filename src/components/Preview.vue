<template>
  <div class="preview" @mousemove="bumpNavButtonOpacity()" ref="preview">
    <button @click="close()" class="cancelButton" title="close this view">
      close/cancel
    </button>
    <div class="previewInner">
      <div class="slideshow" ref="slideshow"></div>
      <div class="inputs fade" ref="inputs">
        <div class="linkButtons">
          <div
            class="visibilityButton"
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
        <AssetData :state="state" :link="link" />
        <div
          v-if="state.multipleLinks()"
          class="leftButton"
          ref = "leftButton"
          @click="state.prev()"
          title="view previous asset [left arrow]"
        ></div>
        <div
          v-if="state.multipleLinks()"
          class="rightButton"
          ref = "rightButton"
          @click="state.next()"
          title="view next asset [right arrow]"
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
import AssetData from './AssetData'

export default {
  name: 'Preview',
  components: { AssetData },
  props: [ 'state', 'link' ],
  data(){
    return {
      asset: null,
      linkType: ''
    }
  },
  computed:{
  },
  methods: {
    bumpNavButtonOpacity(){
      if(!this.state.blockFade) {
        this.$refs.inputs.classList.remove('fade')
        this.$refs.inputs.style.height = this.$refs.inputs.clientHeight + 'px'
        this.$refs.inputs.classList.add('fade')
      }
    },
    close(){
      this.state.closePreview()
    }
  },
  mounted(){
    this.linkType = this.link.filetype.split('/')[0]

    if(this.linkType == 'image' || this.linkType == 'audio'){
      this.asset = document.createElement('div')
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
      this.asset.controls = true
      this.asset.style.position = 'absolute'
      this.asset.style.left = '50%'
      this.asset.style.top = '50%'
      this.asset.style.width = '100%'
      this.asset.style.height = '100%'
      this.asset.style.objectFit = 'contain'
      this.asset.style.background = '#000'
      this.asset.loop = true
      this.asset.style.transform = 'translate(-50%, -50%)'
      this.asset.oncanplay = () => {
        this.asset.play()
        this.state.showLoading = false
      }
      this.$refs.slideshow.appendChild(this.asset)
      this.asset.src = this.state.URLbase + '/' + this.link.href
    }
  },
  beforeUnmount(){
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
  .inputs{
    opacity: 0;
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
</style>