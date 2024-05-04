<template>
  <select
    ref="commentSel"
    @change="updateComSel()"
    :value="state.numComments"
    title="select how many comments you like to see by default, per asset, before clicking 'less' or 'more'"
    class="commentSelection"
  >
    <option value="1"  style="background: #04f4;"  >1</option>
    <option value="2"  style="background: #08c4;"  >2</option>
    <option value="3"  style="background: #0f84;"  >3</option>
    <option value="4"  style="background: #0f44;"  >4</option>
    <option value="5"  style="background: #0f24;"  >5</option>
    <option value="6"  style="background: #4f04;"  >6</option>
    <option value="8"  style="background: #ff04;"  >8</option>
    <option value="10" style="background: #f884;"  >10</option>
    <option value="15" style="background: #f604;"  >15</option>
    <option value="30" style="background: #f404;"  >30</option>
    <option value="50" style="background: #f004;"  >50</option>
  </select>
  comments/asset
</template>

<script>

export default {
  name: 'CommentSelection',
  props: [ 'state' ],
  components: {
  },
  data(){
    return {
    }
  },
  computed:{
  },
  methods: {
    updateComSel(){
      this.state.setUserPref('commentSel', this.$refs.commentSel.value)
    }
  },
  mounted(){
    this.$refs.commentSel.value = this.state.numComments
  },
  watch:{
  }
}
</script>

<style scoped>
  .commentSelection{
    display: inline-block;
    margin-left: 10px;
  }
  .commentSelection{
    appearance: none;
    background-color: transparent;
    display: inline-block;
    padding-left: 0;
    padding-top: 2px;
    margin: 0;
    font-family: inherit;
    cursor: inherit;
    color: #0f8;
    line-height: inherit;
    outline: none;
    width: 50px;
    text-align: center;
    border: 1px solid #40f;
    border-radius: 0.25em;
    font-size: 1.25rem;
    cursor: pointer;
    line-height: 1.1;
    font-weight: 900;
    background-color: #123;
    background-image: linear-gradient(to top, #000, #40f);
  }
</style>
