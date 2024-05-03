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
      :style="`background: #4f8d;${link.comments.length?'':'float:left;margin-left:10px;width:calc(100% - 20px)'}`"
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
        v-for="comment in filteredComments"
        class="commentRow"
      >
        <div class="avatarContainer">
          <div
            @click="state.getUserStats(comment.userID)"
            class="avatar"
            :title="`this comment was posted by ${state.userInfo[comment.userID].name}`"
            :style="`background-image: url(${avatar(comment)})`"
          ></div>
          {{state.shortText(this.state.userInfo[comment.userID].name, 18)}}
        </div>
        
        <div class="commentHeader">
          <div
            v-if="+comment.userID == +state.loggedinUserID"
          >
            <span class="headerText" v-html="header(comment, !comment.edited)"></span>
            <span
              v-if="comment.edited"
              class="edited"
            >
              [edited]
            </span>
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
        </div>

        <span
          class="commentText"
          v-html="comment.text"
          v-if="!comment.editing"
        ></span>
        <textarea v-else type="text"
          ref="commentEdit"
          class="editCommentInput"
          @keyup="state.updateComment(comment)"
          v-model="comment.text"
        ></textarea>
        <div style="clear: both;"></div>
        <!--
        <button
          v-if="mode!='multi' && checked(comment)"
          class="toolbarButtons commentsButton"
          style="color: #fff; z-index:1000; min-width: unset; height: 27px; background: #84fd;margin: unset;margin-top: -1px; margin-right:5px; padding: 0;font-size:32px;padding-top:7px;position: absolute; margin-left: 28px;"
          @click.stop.prevent="this.state.viewComment(comment, links.slug)"
          title="view this item, in this comment"
        >👁</button> -->
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
    filteredComments(){
      let ret = [] // reverse-sort comments
      this.link.comments.map((v,i) => {
        ret = [...ret, this.link.comments[this.link.comments.length-i-1]]
      })
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
        this.$nextTick(() => {
          this.$refs.commentEdit.focus()
        })
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
    header(comment, full){
      return this.state.prettyDate({date: comment.date}, full)
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
    width: 100%;
    background: #123;
    line-height: 28px;
    margin-top: 5px;
    padding-left: 2px;
    padding-top: 2px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #0ff4;
    /*position: absolute;*/
    z-index: 50;
    max-width: 340px;
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
  .headerText{
    color: #ff0;
    margin: 2px;
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
  .avatarContainer{
    display: inline-block;
    float: left;
    margin: 3px;
  }
  .avatar{
    margin-left: 0;
    width: 80px;
    height: 45px;
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
  .editCommentInput:focus{
    outline: none;
  }
  .editCommentInput{
    background: #000;
    border: 4px solid #f084;
    width: 100%;
    vertical-align: top;
    font-size: 14px;
    color: #fff;
    min-height: 60px;
  }
  .edited{
    font-size: .9em;
    color: #ade;
  }
</style>
