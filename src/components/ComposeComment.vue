<template>
  <div class="modalInner">
    <button
      v-if="!this.state.uploadInProgress"
      @click="state.closePrompts()"
      class="cancelButton"
      title="close this view [ESC]"
    >
      close/cancel
    </button>
    <div class="composeComment" ref="composeComment" tabindex="1000">
      <Link
        :state="state"
        :link="state.composeCommentLink"
        :omitAssetData="true"
      /><br>
      <input
        @keydown.enter="submit()"
        v-model="state.newComment"
        type="text"
        ref="commentInput"
        maxlength="1024"
        class="commentInput"
        placeholder="enter your comment..."
      ><br><br>
      <button
        :disabled="!state.newComment"
        :class="{'disabled': !state.newComment}"
        @click.stop.prevent="submit()"
        class="submitCommentButton"
      >
        post comment
      </button>
    </div>
  </div>
</template>

<script>
import Link from './Link'

export default {
  name: 'ComposeComment',
  props: [ 'state' ],
  components:{
    Link
  },
  methods: {
    submit(){
      this.state.submitComment()
    }
  },
  mounted(){
    this.$refs.commentInput.focus()
  }
}
</script>

<style scoped>
  .composeComment{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    font-size: 14px;
  }
  .disabled{
    background: #333!important;
    color: #888;
    text-shadow: 0 0 3px #fff!important;
  }
  .commentInput{
    border: 1px solid #40f;
    width: calc(100vw - 80px);
    max-width: 700px;
    background: #102;
    font-size: 16px;
    margin-top: 50px;
    color: #fff;
  }
  .submitCommentButton{
    background: #0f8;
    border-radius: 25px;
  }
</style>
