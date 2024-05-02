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
      :style="`background: #4f8d;${link.comments.length?'':'float:left;margin-left:10px;'}`"
      title="view and edit your comments"
      v-html="link.comments.length ? 'comment' : 'be first to comment'"
    ></button><br>
    <div
      ref="commentList"
      class="commentList"
      :class="{'show': showComment, 'hide': !showComment}"
      v-if="link.comments.length"
    >
      <div
        v-for="comment in link.comments"
        class="commentRow"
      >
        <div
          @click="state.getUserStats(comment.userID)"
          class="avatar"
          :title="`this comment was posted by ${state.userInfo[comment.userID].name}`"
          :style="`background-image: url(${avatar(comment)})`"
        ></div>
        <div style="clear: both;"></div>
        
        <div class="commentHeader">
          <div
            v-if="+comment.userID == +state.loggedinUserID"
          >
            <button
              class="commentButton"
              style="background:#4f8"
              title="edit comment text"
              v-if="!comment.editing"
              @click.stop.prevent="editComment(comment)"
            >edit</button>
            <button
              v-else
              class="commentButton"
              style="background:#4f8"
              title="finish editing your comment - changes are recorded in real time"
              @click.stop.prevent="closeComment(comment)"
            >close edit</button>

            <button
              class="commentButton"
              style="background:#f04"
              title="permanently delete this comment"
              @click.stop.prevent="deleteComment(comment)"
            >delete</button>
          </div>
          {{header(comment)}}<br>
        </div>

        <span
          class="commentText"
          v-html="comment.text"
          v-if="!comment.editing"
        </span>
        <input v-else type="text"
          ref="commentEdit"
          class="editCommentInput"
          autofocus
          @keyup="state.updateComment(comment)"
          v-model="comment.text"
        >
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
    closeComment(comment){
      this.state.editingComment = false
      comment.editing = false
    },
    editComment(comment){
      this.state.editingComment = true
      comment.editing = true
      this.$nextTick(() => {
        this.$refs.commentEdit.focus()
      })
    },
    avatar(comment){
      if(this.state.userInfo[comment.userID].avatar.indexOf('avatarDefault.png') != -1){
        return this.state.URLbase + '/avatarDefault.png'
      }else{
        return this.state.userInfo[comment.userID].avatar
      }
    },
    deleteComment(comment){
      this.state.deleteComment(comment)
    },
    header(comment){
      return this.state.shortText(this.state.userInfo[comment.userID].name, 18) + ' : ' + this.state.prettyDate({date: comment.date})
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
      }else{
        this.showComment = !this.showComment
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
      //if(val) this.showComment = false
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
    font-size:12px;
    color:#4f8;
  }
  .commentHeader{
    color: #ff0;
    display: block;
  }
  .commentText{
    font-size: 16px;
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
    display: block;
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
  .commentButton{
    padding:0;
    min-width: 60px;
    line-height: 14px;
    font-size: 13px;
    padding-top: 1px;
    margin: 2px;
    padding-left: 2px;
    padding-right: 2px;
  }
  .editCommentInput{
    background: #000;
  }
</style>
