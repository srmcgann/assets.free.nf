<template>
  <div class="votes">
    <div class="votingTitle" v-html="vt"></div>
    <span
      v-for="idx in numv"
      :ref="`vel_${idx}`"
      class="vel"
      v-html="'❤'"
      @click="click(idx)"
      @mouseover="mouseover(idx)"
      @mouseout="clearVel()"
    ></span>
  </div>
</template>

<script>

export default {
  name: 'Votes',
  props: [ 'state', 'link' ],
  data(){
    return {
      numv: 6,
      votes: 0
    }
  },
  methods: {
    clearVel(){
      console.log('refs: ', this.$refs)
      for(let j=0;j<this.numv;j++){
        let el = this.$refs[`vel_${j}`]
        el.style.color='#40f'
        el.style.textShadow = '5px 5px 5px #000'
        if(j<this.votes) el.style.color='red'
      }
    },
    click(idx){
      this.votes = this.votes == idx+1 ? 0 : idx+1
      this.clearVel()
    },
    mouseover(idx){
      this.clearVel()
      let vel = this.$refs[`vel_${idx}`]
      vel.style.textShadow = '0 0 20px #fff'
      for(let j=0;j<=idx;j++){
        this.$refs[`vel_${j}`].style.color='#0f8'
      }
    }
  },
  computed:{
    vt(){
      return `your vote: ${this.votes}`
    }
  },
  mounted(){
    Array(this.numv).fill().map((v, i) => {
      let vel = this.$refs[`vel_${i}`]
      if(i<this.votes) vel.style.color='red'
    })
  }
}
</script>

<style scoped>
  *{
    box-sizing: border-box;
  }
  .votes{
    text-shadow: 2px 2px #000;
    border-radius: 20px;
    text-align: center;
    width: 100%;
    height: 65px;
    background: #208;
    display: inline-block;
    vertical-align: top;
    font-size: 200%;
    line-height: 95%;
    border: 2px solid #40f;
  }
  .vel{
    text-align: center;
    display: inline-block;
    width: 16%;
    text-shadow: 5px 5px 5px #000;
    cursor: pointer;
    -webkit-user-select: none; /* Safari */        
   -moz-user-select: none; /* Firefox */
   -ms-user-select: none; /* IE10+/Edge */
    user-select: none; /* Standard */
  }
  .votingTitle{
    color: #48f;
    font-weight: 900;
    line-height: 20px;
    height: 26px;
    font-size: 18px;
    text-align: left;
    padding-left: 4%;
  }
</style>
