<template>
  <div
    class="loadingAnimation"
    ref="loadingAnimation"
  >
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
      S: Math.sin,
      C: Math.cos,
      w: 0,
      h: 0,
      t: 0,
      ipx: 0,
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
        this.x.lineWidth = Math.min(1000,100*lwo/this.Z)
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
      this.oZ=28
      this.x.globalAlpha = 1
      this.x.clearRect(0,0,this.c.width,this.c.height)
      this.x.lineJoin = this.x.lineCap = 'roud'
      
      this.x.fillStyle='#0f44'
      this.x.fillRect(0,0,this.c.width*this.percent,this.c.height)

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
          let q = (this.percent<1?this.t*8:this.t)+(m?Math.PI:0)
          for(let i=sd; i--;){
            this.X = tx + w/sd*i*sp
            this.Y = ty + this.S(p=Math.PI*2/sd*i/r + q + q2)*ls1
            this.Z = tz + this.C(p)*ls1
            this.Z += this.oZ
            if(this.Z>0) this.x.lineTo(...this.Q())
            this.X = tx + w/sd*(i+1)*sp
            this.Y = ty + this.S(p=Math.PI*2/sd*(i+1)/r + q + q2)*ls1
            this.Z = tz + this.C(p)*ls1
            this.Z += this.oZ
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
          let col1 = `hsla(${this.percent<1?m*180:120},99%,50%,.7)`
          this.stroke(col1,'', 4, true)
        }
        if(!this.ipx) this.ipx = (l1[0]+l2[0])/2
      }
      if(typeof l1 != 'undefined'){
        this.x.font = (fs = 85) + "px Courier Prime"
        this.x.fillStyle = '#fff'
        this.ipx += (((l1[0]+l2[0])/2+fs/2) - this.ipx)/4
        s = 100
        if(typeof this.state.starImgs[6] != 'undefined' && 
           typeof this.state.starImgs[1] != 'undefined' &&
           this.state.starImgs[6].loaded &&
           this.state.starImgs[1].loaded &&
           this.percent<1){
          this.x.drawImage(this.state.starImgs[6].img,l1[0]-s/2,-s/2+l1[1],s,s)      
          this.x.drawImage(this.state.starImgs[1].img,l2[0]-s/2,-s/2+l2[1],s,s)      
        }else{
          if(typeof this.state.starImgs[4] != 'undefined' &&
            this.state.starImgs[4].loaded){
            this.x.drawImage(this.state.starImgs[4].img,l1[0]-s/2,-s/2+l1[1],s,s)
            this.x.drawImage(this.state.starImgs[4].img,l2[0]-s/2,-s/2+l2[1],s,s)
          }
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
    this.$refs.loadingAnimation.appendChild(this.c)
    this.x = this.c.getContext('2d')
    this.c.width = 1920
    this.c.height = 200
    this.c.style.height = '83px'
    this.c.style.width = '800px'
    this.c.style.verticalAlign = 'top'
    this.Draw()
  },
  beforeUnmount(){
    this.break = true
  }
}
</script>

<style scoped>
.loadingAnimation{
  display: block;
  max-width: calc(100% - 50px));
  border-bottom: 4px solid #40f8;
}
canvas{
  background:#0000;
}
</style>
