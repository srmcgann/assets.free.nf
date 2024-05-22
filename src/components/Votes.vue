<template>
  <div class="votes">
    <div class="votingTitle" v-html="vt"></div>
    <span
      v-for="idx in numv"
      :id="'vel_'+link.slug+'_'+idx"
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
      votes: 0,
    }
  },
  methods: {
    clearVel(){
      for(let j=0;j<this.numv;j++){
        let el = document.querySelector(`#vel_${this.link.slug}_${j+1}`)
        el.style.color='#40f'
        el.style.textShadow = '5px 5px 5px #000'
        if(j<this.votes) el.style.color='red'
      }
    },
    click(idx){
      this.votes = this.votes == idx ? 0 : idx
      this.clearVel()
      this.state.setVote(this.link.slug, this.votes)
    },
    mouseover(idx){
      this.clearVel()
      let vel = document.querySelector(`#vel_${this.link.slug}_${idx+1}`)
      vel.style.textShadow = '0 0 20px #fff'
      for(let j=0;j<idx;j++){
        document.querySelector(`#vel_${this.link.slug}_${j+1}`).style.color='#0f8'
      }
    }
  },
  computed:{
    vt(){
      return `your vote: ${this.votes}`
    }
  },
  mounted(){
    this.votes = this.link.votes
    Array(this.numv).fill().map((v, i) => {
      let vel = document.querySelector(`#vel_${this.link.slug}_${i+1}`)
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
    border-radius: 100px;
    text-align: center;
    width: calc(100% - 10px);
    height: 55px;
    background: #2088;
    display: inline-block;
    vertical-align: top;
    font-size: 150%;
    margin-top:10px;
    line-height: 90%;
    margin-bottom: 10px;
    left: 50%;
    position: relative;
    transform: translate(-50%);
    color: #40f;
    box-shadow: 0 0 10px 10px #0f44;
    max-width: 350px;
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
    text-align: center;
    padding-left: 4%;
  }
</style>
