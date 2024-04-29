<template>
  <div
    class="loadingAnimation"
    ref="loadingAnimation"
  >
    <label v-if="state.loggedIn" class="checkboxLabel" :key="link.linkType+link.ct+'key'">
      <input type="checkbox" v-model="link.selected" @input="updateLinkSelected()">
      <span class="checkmark" style="margin-left: -30px;"></span>
      <span style="font-size:.75em;margin-top:5px;display:block;color:#4f88;padding:0;margin-left:-34px;">select</span><br>
    </label>
    
    <div class="views" v-html="state.views(link)">
    </div>
    
    <div
      @mousedown.stop.prevent
      class="linkThumb"
      ref="linkThumb"
      @click.prevent.stop="state.preview(link)"
      title="view this asset"
    ></div>
    <!--#{{link.ct+1}}-->
    <div class="linkButtons" @mousedown.stop.prevent @click.stop.prevent>
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
    <br>
    <AssetData :state="state" :link="link" />
  </div>
</template>

<script>
import AssetData from './AssetData'

export default {
  name: 'LoadingAnimation',
  components: { AssetData },
  props: [ 'state', 'percent', 'filename' ],
  data(){
    return {
      c: document.createElement('canvas'),
      x: null,
      break: false,
      linkType: '',
      img: null,
      X: 0,
      Y: 0,
      Z: 0,
      oZ: 0,
      w: 0,
      h: 0,
      t: 0,
      ipx: 0,
      icw: 1920,
      ich: 200,
      starImgs: []
    }
  },
  computed: {
  },
  methods: {
    updateLinkSelected(){
      if(this.link.selected){
        this.link.selected = false
      }else{
        this.link.selected = true
      }
    },
    getThumb(){
      let l
      try{
        this.img.src = this.linkType == 'audio' ? this.state.URLbase + '/' + 'musicNotes.svg' : this.state.URLbase + '/' + 'thumb.php?res=resources/' + (this.link.originalSlug + '.' + this.link.href.split('.')[1])
      } catch(error){
        console.log(error)
      }
    },
    stroke(scol, fcol, lwo=1, od=true, oga=1){
      if(scol){
        //x.closePath()
        if(od) this.x.globalAlpha = .2*oga
        this.x.strokeStyle = scol
        this.x.lineWidth = Math.min(1000,100*lwo/Z)
        if(od) this.x.stroke()
        this.x.lineWidth /= 4
        this.x.globalAlpha = 1*oga
        this.x.stroke()
      }
      if(fcol){
        this.x.globalAlpha = 1*oga
        this.x.fillStyle = fcol
        this.x.fill()
      }
    },
    Q(){
      return [this.c.width/2+this.X/this.Z*900, this.c.height/2+this.Y/this.Z*900]
    },
    Draw(){
      this.x.globalAlpha = 1
      this.x.fillStyle='#000f'
      this.x.fillRect(0,0,this.c.width,this.c.height)

      if(!this.t){
        var starsLoaded = false
        this.starImgs = [{loaded: false}]
        this.starImgs = Array(9).fill().map((v,i) => {
          let a = {img: new Image(), loaded: false}
          a.img.onload = () => {
            a.loaded = true
            setTimeout(()=>{
              if(this.starImgs.filter(v=>v.loaded).length == 9) starsLoaded = true
            }, 0)
          }
          a.img.src = `${this.state.URLbase}/star${i+1}.png`
          return a
        })
        this.oZ=28
      }


      this.x.globalAlpha = 1
      this.x.fillStyle='#000f'
      this.x.fillRect(0,0,this.c.width,this.c.height)
      this.x.lineJoin = this.x.lineCap = 'round'

      let p, l1, l2, fs, s
      this.x.textAlign = 'left'
      for(let j=0;j<99*this.percent|0;j++){
        let sd = 1
        let w = 1
        let sp = .5
        let tx = (j-50) * sp + (-w/2+.5) * sp - 3.5
        let ty = -1
        let tz = 0
        let ls1 = sp*2
        let r = 16
        let q2 = Math.PI * 2 / r * j
        for(let m=2;m--;) {
          this.x.beginPath()
          let q = (this.percent<1?t*8:t)+(m?Math.PI:0)
          for(let i=sd; i--;){
            this.X = tx + w/sd*i*sp
            this.Y = ty + S(p=Math.PI*2/sd*i/r + q + q2)*ls1
            this.Z = tz + C(p)*ls1
            this.Z += oZ
            if(this.Z>0) this.x.lineTo(...this.Q())
            this.X = tx + w/sd*(i+1)*sp
            this.Y = ty + S(p=Math.PI*2/sd*(i+1)/r + q + q2)*ls1
            this.Z = tz + C(p)*ls1
            this.Z += oZ
            if(this.Z>0){
              if(m){
                l1 = this.Q()
                this.x.lineTo(...l1)
              }else{
                l2 = this.Q()
                this.x.lineTo(...l2)
              }
            }
          }
          col1 = `hsla(${this.percent<1?m*180:120},99%,50%,.7)`
          this.stroke(col1,'', 4, true)
        }
        if(!this.ipx) this.ipx = (l1[0]+l2[0])/2
      }
      if(typeof l1 != 'undefined'){
        this.x.font = (fs = 85) + "px Courier Prime"
        this.x.fillStyle = '#fff'
        this.ipx += (((l1[0]+l2[0])/2+fs/2) - this.ipx)/4
        s = 100
        if(this.percent<1){
          this.x.drawImage(this.starImgs[6].img,l1[0]-s/2,-s/2+l1[1],s,s)      
          this.x.drawImage(this.starImgs[1].img,l2[0]-s/2,-s/2+l2[1],s,s)      
        }else{
          this.x.drawImage(this.starImgs[4].img,l1[0]-s/2,-s/2+l1[1],s,s)
          this.x.drawImage(this.starImgs[4].img,l2[0]-s/2,-s/2+l2[1],s,s)
        }
        this.x.fillText((Math.round(this.percent*100)/1) + '%', this.ipx+fs*.25, this.c.height/2 - fs/16)
      }
      
      this.x.fillStyle = '#8888'
      this.x.textAlign = 'center'
      this.x.fillText(this.filename,this.c.width/2,this.c.height/2+fs)
      
      this.t+=1/60
      if(!this.break) requestAnimationFrame(this.Draw)
    }
  },
  mounted(){
    this.$refs.linkThumb.appendChild(this.c)
    this.x = this.c.getContext('2d')
    this.linkType = this.link.filetype.split('/')[0]
    this.c.width = 1920
    this.c.height = 200
    this.c.style.width = 'calc(100% - 40px)'
    this.c.style.height = '113px'
    this.$refs.loadingAnimation.onresize = () =>{
      this.c.style.height = this.c.clientWidth*(this.ich/this.icw) + 'px'
    }
    this.c.style.borderRadius = '20px'
    this.Draw()
  },
  beforeUnmount(){
    this.break = true
  }
}
</script>

<style scoped>
loadingAnimation{
  position: relative;
  display: block;
  width: 100%;
  z-index: 1000;
}
canvas{
  background:#000;
  position: absolute;
  left: 50%;
  top: 50%;
  border: 1px solid #4f82;
  transform: translate(-50%, -50%);
}
</style>
