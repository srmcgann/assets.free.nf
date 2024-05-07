<template>
  <div
    class="linkComments"
    :class="{'linkCommentsPreviewMode': state.showPreview}"
  >
    <div class="comButNavLabel" v-if="link.comments.length">
      show
      <br>
      <button
        @click.stop.prevent="decrementNumComments()"
        class="expandInfoButton lessButton"
        :disabled="numComments==0"
        :class="{'disabledButton': numComments==0}"
        title="show less-common/extra details about this asset"
      >
        less
      </button>

      <button
        @click.stop.prevent="incrementNumComments()"
        class="expandInfoButton"
        :disabled="numComments==link.comments.length"
        :class="{'disabledButton': numComments>=link.comments.length}"
        title="show less-common/extra details about this asset"
      >
        more
      </button>
    </div>
    
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="manageComments()"
      class="assetDataButton noCommentsButton"
      title="view and edit your comments"
      v-if="!link.comments.length"
    >be first to comment</button>
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="manageComments()"
      class="assetDataButton addCommentButton"
      title="view and edit your comments"
      style="padding: 2px;"
      v-else-if="!state.showComposeComment"
    >add comment</button><br>
  </div>
  <div
    ref="commentList"
    class="commentList"
    @mousemove="state.bumpADOpacity++"
    v-if="link.comments.length && numComments"
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
          </span><br>
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
      <textarea v-if="comment.editing"
        ref="commentEdit"
        :key="'comment'+comment.id"
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
    <div
      v-if="link.comments.length > numComments"
      class="commentRow"
    >
      <button
        class="expandInfoButton lessButton wideLB"
        :disabled="numComments==0"
        :class="{'disabledButton': numComments==0}"
        title="show fewer comments"
        @click.stop.prevent="decrementNumComments()"
      >show less comments</button>
      <button
        class="expandInfoButton wideLB"
        :disabled="numComments==link.comments.length"
        :class="{'disabledButton': numComments>=link.comments.length}"
        title="load more comments"
        @click.stop.prevent="incrementNumComments()"
      >load more comments</button>
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
      commentIncrVal: 1,
      numComments: this.state.numComments
    }
  },
  computed:{
    filteredComments(){
      let ret = this.link.comments
      ret.sort((a,b) => a.id - b.id)
      return ret.filter((v, i) => i<this.numComments)
    },
    colHeight(){
      return Math.min(200, this.link.comments.length*28+5) + 'px'
    }
  },
  methods: {
    decrementNumComments(){
      this.numComments= Math.max(0, this.numComments-this.commentIncrVal)
    },
    incrementNumComments(){
      this.numComments= Math.min(this.link.comments.length, this.numComments+this.commentIncrVal)
      this.$nextTick(() => {
        this.$refs.commentList.scrollTo(0, 1e6)
      })
    },
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
                                          // uncomment below to omit/replace time @ "edited"
      return this.state.prettyDate({date: comment.date}, true) //, full) 
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
    'state.numComments'(val){
      this.numComments = val
    },
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
    padding-top: 2px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #0ff4;
    border-radius: 10px;
    z-index: 50;
    max-height: 340px;
    max-width: 700px;
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
    font-size: 12px;
    color: #ff0;
  }
  .avatar{
    margin-left: 0;
    width: 80px;
    height: 45px;
  }
  .linkComments{
    position: absolute;
    margin-left: 109px;
    margin-top: -24px;
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
  .noCommentsButton{
    background:#4f8d;
    position:absolute;
    margin-top: -2px;
    width: 197px;
    line-height: 12px;
    height: 23px;
    margin-left: 13px;
  }
  .assetDataButton{
    background: #4f8d;
    height: 23px;
  }
  .addCommentButton{
    padding: 2px;
    line-height: 16px;
    font-size: 13px;
    vertical-align: bottom;
    position: absolute;
    margin-top: -2px;
    height: 23px;
    margin-left: -7px;
  }
  .edited{
    font-size: .9em;
    color: #ade;
  }
  .lessButton{
    background: #604;
    color: #fff;
  }
  .comButNavLabel{
    display: inline-block;
    text-align: center;
    width:124px;
    color: #f80;
    font-size:9px;
    line-height: 0;
    vertical-align: top;
  }
  .expandInfoButton{
    float: unset;
    display: inline-block;
    margin: 3px;
    margin-left: 0;
    margin-top: 0;
    margin-bottom: 0;
    width: 46px;
  }
  .linkCommentsPreviewMode{
    position: absolute!important;
    margin-top: -24px!important;
    right: calc(20% + 170px)!important;
  }
  .bumpDown{
    margin-top:25px;
  }
  .wideLB{
    width: 134px;
    margin-bottom: 6px;
    left:50%;
    transform:translate(-155px);
    margin-left:10px;
    margin-right:10px;
  }
</style>
