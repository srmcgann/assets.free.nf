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
      linkType: '',
      img: null,
      X: 0,
      Y: 0,
      Z: 0,
      oX: 0,
      oY: 0,
      oZ: 0,
      w: 0,
      h: 0,
      t: 0,
      icw: 1920,
      ich: 200
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
    Draw(){
      this.x.globalAlpha = 1
      this.x.fillStyle='#000f'
      this.x.fillRect(0,0,this.c.width,this.c.height)

      var R=(Rl,Pt,Yw,m)=>{
        if(m){
          X+=oX
          Y+=oY
          Z+=oZ
        }
      }
      var Q=()=>[c.width/2+X/Z*900,c.height/2+Y/Z*900]

      if(!this.t){
        var stroke = (scol, fcol, lwo=1, od=true, oga=1) => {
          if(scol){
            //x.closePath()
            if(od) x.globalAlpha = .2*oga
            x.strokeStyle = scol
            x.lineWidth = Math.min(1000,100*lwo/Z)
            if(od) x.stroke()
            x.lineWidth /= 4
            x.globalAlpha = 1*oga
            x.stroke()
          }
          if(fcol){
            x.globalAlpha = 1*oga
            x.fillStyle = fcol
            x.fill()
          }
        }

        var starsLoaded = false, starImgs = [{loaded: false}]
        var starImgs = Array(9).fill().map((v,i) => {
          let a = {img: new Image(), loaded: false}
          a.img.onload = () => {
            a.loaded = true
            setTimeout(()=>{
              if(starImgs.filter(v=>v.loaded).length == 9) starsLoaded = true
            }, 0)
          }
          a.img.src = `https://srmcgann.github.io/stars/star${i+1}.png`
          return a
        })
      }

      this.oX=0
      this.oY=0
      this.oZ=28

      this.x.globalAlpha = 1
      this.x.fillStyle='#000f'
      this.x.fillRect(0,0,c.width,c.height)
      this.x.lineJoin = x.lineCap = 'round'

      this.percent
      x.textAlign = 'left'
      for(j=0;j<99*this.percent|0;j++){
        sd = 1
        w = 1
        sp = .5
        tx = (j-50) * sp + (-w/2+.5) * sp - 3.5
        ty = -1
        tz = 0
        ls1 = sp*2
        r = 16
        q2 = Math.PI * 2 / r * j
        for(m=2;m--;) {
          x.beginPath()
          q = (this.percent<1?t*8:t)+(m?Math.PI:0)
          for(i=sd; i--;){
            X = tx + w/sd*i*sp
            Y = ty + S(p=Math.PI*2/sd*i/r + q + q2)*ls1
            Z = tz + C(p)*ls1
            R(Rl,Pt,Yw,1)
            if(Z>0) x.lineTo(...Q())
            X = tx + w/sd*(i+1)*sp
            Y = ty + S(p=Math.PI*2/sd*(i+1)/r + q + q2)*ls1
            Z = tz + C(p)*ls1
            R(Rl,Pt,Yw,1)
            if(Z>0){
              if(m){
                l1 = Q()
                x.lineTo(...l1)
              }else{
                l2 = Q()
                x.lineTo(...l2)
              }
            }
          }
          col1 = `hsla(${this.percent<1?m*180:120},99%,50%,.7)`
          stroke(col1,'', 4, true)
        }
        if(typeof ipx == 'undefined') ipx = (l1[0]+l2[0])/2
      }
      if(typeof l1 != 'undefined'){
        x.font = (fs = 85) + "px Courier Prime"
        x.fillStyle = '#fff'
        ipx += (((l1[0]+l2[0])/2+fs/2) - ipx)/4
        //x.lineWidth = 5
        //x.strokeStyle='#000'
        //x.strokeText((Math.round(this.percent*100)/1) + '%', ipx+fs*.25, c.height/2 + fs/3)
        s = 200
        x.drawImage(starImgs[this.percent<1?((t*10|0)%2?1:6):4].img,ipx-fs*1.5,c.height/2-s/1.6,s,s)
        x.fillText((Math.round(this.percent*100)/1) + '%', ipx+fs*.25, c.height/2 - fs/16)
      }
      
      filename = "abcdefghijklm...xyz0123456789.exe"
      x.fillStyle = '#8888'
      x.textAlign = 'center'
      x.fillText(filename,c.width/2,c.height/2+fs)

      
      requestAnimationFrame(this.Draw)
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
    if(this.linkType == 'video'){
      this.img = document.createElement('video')
      this.img.loop = true
      this.img.muted = true
      this.img.oncanplay = () => {
        this.w = this.img.videoWidth
        this.h = this.img.videoHeight
        this.img.play()
        this.Draw()
      }
      this.img.src = `${this.state.URLbase}/resources/` + this.link.originalSlug + '.' + this.link.href.split('.')[1]
    }
    if(this.linkType == 'image' || this.linkType == 'audio'){
      this.img = new Image()
      this.img.onload = () => {
        this.w = this.img.width
        this.h = this.img.height
        this.Draw()
      }
      this.getThumb()
    }
  }
}
</script>

<style scoped>
  .link:hover{
    background-color: #408a;
  }
  .link{
    display: inline-block;
    color: #acd;
    background-color: #002a;
    font-size: 20px;
    box-sizing: border-box;
    text-align: left;
    margin: 5px;
    word-break: break-word;
    border-radius: 10px;
    vertical-align: top;
    border: 3px solid #4086;
    padding: 5px;
    min-width: 324px
  }
  .href{
    font-size: .6em;
    color: #0ff;
    margin: 5px;
    padding: 3px;
    width: calc(100% - 10px);
  }
  .linkThumb{
    float: left;
    width: 200px;
    height: 113px;
    margin: 5px;
    margin-top: 10px;
    cursor: pointer;
    background-size: contain;
    background-position: 20px 20px;
    background-repeat: no-repeat;
    background-color: #000;
    border-radius: 20px;
  }
  .views{
    position: relative;
    min-width: 124px;
    text-align: right;
    right: 10px;
  }
  .visibilityButton{
    width: 64px;
    height: 45px;
    background-size: 49px 49px;
  }
</style>
