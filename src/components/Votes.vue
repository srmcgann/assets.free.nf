<template>
  <div class="votes">
    <div class="votingTitle" v-html="vt"></div>
    <span
      v-for="idx in state.numv"
      :id="'vel_'+link.slug+'_'+idx"
      class="vel"
      v-html="'☻'"
      @click="click(idx)"
      @mouseover="mouseover(idx)"
      @mouseout="clearVel()"
    ></span>
    <table>
      <tr>
        <td class="tdLeft">popularity</td>
        <td v-if="!!(+link.votesCast)" class="tdRight">
          <div class="pop">
            <div class="popInner" :style="popStyle(link)"></div>
          </div>
        </td>
        <td v-else class="tdRight">
          no votes yet!
        </td>
      </tr>
      <tr v-if="link.expandedInfo && !!(+link.votesCast)">
        <td class="tdLeft">upvotes</td><td class="tdRight" v-html="state.voteRating(link)"></td>
      </tr>
    </table>
  </div>
</template>

<script>

export default {
  name: 'Votes',
  props: [ 'state', 'link' ],
  data(){
    return {
      votes: 0,
    }
  },
  methods: {
    popStyle(link){
      let perc = this.state.voteRatingPerc(link)
      return `width:${perc*100}%; background: hsla(${200*perc}, 99%, 50%, 1);`
    },
    clearVel(){
      for(let j=0;j<this.state.numv;j++){
        let el = document.querySelector(`#vel_${this.link.slug}_${j+1}`)
        el.style.color='#40f'
        el.style.textShadow = '3px 3px 5px #000'
        if(j<this.votes) el.style.color='red'
      }
    },
    click(idx){
      if(!this.state.loggedIn){
        this.state.showRegister = false
        this.state.showLoginPrompt = true
        this.state.pendingVoteSlug = this.link.slug
        this.state.pendingVoteValue = this.votes        
        this.state.votePending = true
        return
      }else{
        this.votes = this.votes == idx ? 0 : idx
        this.clearVel()
        this.state.setVote(this.link.slug, this.votes)
      }
    },
    mouseover(idx){
      this.clearVel()
      let vel = document.querySelector(`#vel_${this.link.slug}_${idx}`)
      vel.style.textShadow = '0 0 20px #fff'
      for(let j=0;j<idx;j++){
        document.querySelector(`#vel_${this.link.slug}_${j+1}`).style.color='#0f8'
      }
    }
  },
  watch:{
    'link.votes'(val){
      this.votes = val
    }
  },
  computed:{
    vt(){
      return `your vote: ${this.votes}/${this.state.numv}`
    }
  },
  mounted(){
    this.votes = this.link.votes
    Array(this.state.numv).fill().map((v, i) => {
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
    border-radius: 100px;
    text-align: left;
    width: calc(100% - 10px);
    height: 155px;
    background: #2088;
    display: inline-block;
    vertical-align: top;
    font-size: 200%;
    margin-top:10px;
    line-height: 50%;
    margin-bottom: 10px;
    left: 50%;
    position: relative;
    transform: translate(-50%);
    color: #40f;
    box-shadow: 0 0 20px 0px #80f;
    width: 350px;
  }
  .vel{
    display: inline-block;
    width: 34px;
    text-shadow: 3px 3px 5px #000;
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
