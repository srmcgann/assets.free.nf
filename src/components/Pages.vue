<template>
  <div class="pages" v-if="(state.totalPages > 0 || state.totalUserPages > 0) && state.mode != 'item' || (state.search.string && state.totalPages>1)" :class="{'bumpLeft': !state.loggedIn}">
    <button
      class="navButton"
      :class="{'disabled': curPage < 1}"
      :disabled="curPage < 1"
      title="go to first page [alt + home]"
      @click="state.firstPage()"
    >
      &lt;&lt;
    </button>
    <button
      class="navButton"
      :disabled="curPage < 1"
      title="go back one page  [alt + left arrow]"
      :class="{'disabled': curPage < 1}"
      @click="state.regressPage()"
    >
      &lt;
    </button>
    {{pagenumber}}
    <button
      class="navButton"
      :class="{'disabled': totalPages == curPage+1}"
      :disabled="totalPages == curPage+1"
      title="go forward one page  [alt + right arrow]"
      @click="state.advancePage()"
    >
      &gt;
    </button>
    <button
      class="navButton"
      :class="{'disabled': totalPages == curPage+1}"
      title="go to last page  [alt + end]"
      :disabled="totalPages == curPage+1"
      @click="state.lastPage()"
    >
      &gt;&gt;
    </button>
  </div>
</template>

<script>
export default {
  name: 'Pages',
  props: [ 'state' ],
  data(){
    return {
    }
  },
  methods: {
  },
  computed: {
  },
  mounted(){
    if(this.curPage > this.totalPages) this.state.jumpToPage(this.totalPages)
  }
}
</script>

<style scoped>
  .pages{
    position: absolute;
    display: inline-block;
    /*width: 270px;*/
    line-height: .8em;
    min-height: 25px;
    right: 10px;
    margin-top: -45px;
    vertical-align: top;
    padding-top: 0px;
    font-size: .8em;
    z-index: 1000;
  }
  .navButton{
    min-width:0;
    height: 25px;
    padding: 0;
    background: #0f0;
    margin: 0;
    margin-left: 2px;
    margin-right: 2px;
    width: 25px;
  }
  .disabled{
    background: #888;
  }
</style>
