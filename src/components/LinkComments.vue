<template>
  <div class="linkComments">
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="toggleShowComments()"
      class="assetDataButton"
      style="background: #84fd; color: #fff;"
      title="show or hide comments"
      v-html="showComment?`close`:`show (${link.comments.length})`"
      v-if="!!link.comments.length"
    >
    </button>
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="manageComments()"
      class="assetDataButton"
      style="background: #4f8d"
      title="view and edit your comments"
      v-html="link.comments.length ? 'comment' : 'be the first to comment'"
    ></button><br>
    <div
      @mousedown.stop.prevent
      ref="commentList"
      class="commentList"
      :class="{'show': showComment, 'hide': !showComment}"
    >
      <div
        v-for="comment in link.comments"
        class="commentRow"
      >
        <div
          @click="state.getUserStats(link.userID)"
          class="avatar"
          :title="`this comment was posted by ${state.userInfo[link.userID]?.name}`"
          :style="`background-image: url(${avatar})`"
        ></div>
        
        <span class="commentHeader">
          <button
            class="commentButton"
            @click.stop.prevent="editComment(comment)"
          >edit</button>

          <button
            class="commentButton"
            @click.stop.prevent="deleteComment(comment)"
          >delete</button>
          <br>
        
          {{header(comment)}}
        </span>

        <span
          class="commentText"
          v-html="comment.text"
        </span>
        
        
        <!--
        <label
          class="checkboxLabel commentLabel"
        >
          <input
            :checked="checked(comment)"
            type="checkbox"
            @change="updateSelection($event, comment)"
          >
          <span class="checkmark" style="margin-left: -30px;"></span>
          <div
            @click="state.getUserStats(link.userID)"
            class="avatar"
            :title="`this comment was posted by ${state.userInfo[link.userID]?.name}`"
            :style="`background-image: url(${avatar});`"
          ></div>
          
          <span
            class="commentText"
            :style="`font-size:16px; margin-left:${checked(comment) ? '26px':'-10px'};`"
             v-html="comment.text"
          </span>
        </label>
        -->
        
        <!--
        <button
          v-if="mode!='multi' && checked(comment)"
          class="toolbarButtons commentsButton"
          style="color: #fff; z-index:1000; min-width: unset; height: 27px; background: #84fd;margin: unset;margin-top: -1px; margin-right:5px; padding: 0;font-size:32px;padding-top:7px;position: absolute; margin-left: 28px;"
          @click.stop.prevent="this.state.viewComment(comment, links.slug)"
          title="view this item, in this comment"
        >👁</button> -->
        <br>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'commentSelection',
    // "links" prop might receive an array of links (mode == 'multi'),
    //  or a single link (no array, mode != 'multi')
  props: [ 'state', 'link'],
  components: {
  },
  data(){
    return {
      showComment: false,
    }
  },
  computed:{
    avatar(){
      if(this.state.userInfo[this.link.userID]?.avatar.indexOf('avatarDefault.png') != -1){
        return this.state.URLbase + '/avatarDefault.png'
      }else{
        return this.state.userInfo[this.link.userID]?.avatar
      }
    },
    filteredcomments(){
      let ret = ['none']
      ret = [...ret, ...this.link.comments]
      return ret
    },
    colHeight(){
      return Math.min(200, this.link.comments.length*28+5) + 'px'
    }
  },
  methods: {
    editComment(comment){
      console.log('editing comment')
    },
    deleteComment(comment){
      console.log('deleting comment')
    },
    header(comment){
      return this.state.shortText(this.state.userInfo[comment.userID].name, 10) + ' : ' + this.state.prettyDate({date: comment.date}) + "<br>"
    },
    checked(comment){
      switch(this.mode){
        case 'multi':
          let checked = false
          this.links.map(v=>{
            //if(!!Comment.meta.slugs.filter(q=>q==v.slug).length) checked = true
          })
          return checked
        break
        default:
          //return !!Comment.meta.slugs.filter(v=>v==this.links.slug).length
        break
      }
    },
    manageComments(){
      this.state.closePrompts()
      this.state.composeCommentLink = this.link
      this.state.showComposeComment = true
    },
    toggleShowComments(){
      this.state.doMouseDown()
      if(!this.showComment){
        this.$nextTick(()=>{
          this.$nextTick(()=>{
            this.showComment = !this.showComment
          })
        })
      }
    },
    pushUpdate(comment){
      let obj = {
        name: comment.name,
        id: comment.id,
        description: comment.meta.description,
        slugs: comment.meta.slugs,
        private: comment.meta.private,
      }
      this.state.updateComment(obj)
    },
    updateSelection(e, comment){
      /*
      let val = e.target.checked
      switch(this.mode){
        case 'multi':
          this.links.map(link=>{
            comment.meta.slugs = comment.meta.slugs.filter(slug=>{
              return slug !== link.slug
            })
            if(val){
              comment.meta.slugs.push(link.slug)
            }
            this.pushUpdate(comment)
          })
        break
        default:
          comment.meta.slugs = comment.meta.slugs.filter(slug=>{
            return slug !== this.links.slug
          })
          if(val){
            comment.meta.slugs.push(this.links.slug)
          }
          this.pushUpdate(comment)
        break
      }
      */
    }
  },
  mounted(){
  },
  watch:{
    'state.click'(val){
      if(val) this.showComment = false
    }
  }
}
</script>

<style scoped>
  .commentSelection{
    display: inline-block;
    margin-left: 10px;
  }
  .show{
    display: inline-block;
  }
  .hide{
    display: none;
  }
  .commentList{
    width: 315px;
    background: #123;
    line-height: 28px;
    margin-top: 5px;
    padding-left: 2px;
    padding-top: 2px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #0ff4;
    position: absolute;
    z-index: 50;
    max-height: 340px;
  }
  .commentLabel:hover{
    background: #0f44;
  }
  .commentLabel{
    display: block;
  }
  .commentHeader, .commentText{
    font-size:14px;
    color:#4f8;
  }
  .commentHeader{
    color: #ff0;
  }
  .checkboxLabel{
    padding-left: unset;
    font-size: 16px;
  }
  input[type=checkbox]{
    margin: 4px;
    margin-right: 0;
  }
  .commentRow{
    display: inline-block;
    margin-bottom: 10px;
    margin: 0;
    line-height: 1em;
    border-bottom: 1px solid #0ff4;
    position: relative;
    width: 100%;
  }
  .avatar{
    margin: 3px;
    margin-left: 0;
    width: 80px;
    height: 45px;
    display: inline-block;
    float: left;
  }
</style>
