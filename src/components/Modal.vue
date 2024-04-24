<template>
  <button
    v-if="!this.state.uploadInProgress"
    @click="close()"
    class="cancelButton"
    title="close this view [ESC]"
  >
    close/cancel
  </button>
  <div class="modal" ref="modal" tabindex="1000">
    <div class="modalInner" v-html="content"></div>
  </div>
</template>

<script>
export default {
  name: 'Modal',
  props: [ 'state', 'content' ],
  methods: {
    close(){
      this.state.closeModal()
    }
  },
  mounted(){
    this.$refs.modal.focus()
  }
}
</script>

<style scoped>
  .modal{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 100vh;
    font-size: 14px;
  }
</style>
