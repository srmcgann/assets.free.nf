<template>
  <div class="collectionTemplate" ref="collectionTempate" tabindex="2000">
    <button
      @click="close()"
      class="cancelButton"
      title="close this view [ESC]"
    >
      close/cancel
    </button>
    <div class="collectionTemplateInner">
      <br><br>
      CREATE A NEW COLLECTION
      <br><br>
      enter details below, click create,<br>
      then browse your assets and add items<br>
      you may also edit this stuff later<br>
      <div class="newCollectionForm">
        <input
          class="collectionFormInput"
          ref="name"
          v-model="name"
          placeholder="collection name"
          @keydown.enter.stop.prevent="submit()"
        ><br>
        <input
          class="collectionFormInput"
          v-model="description"
          placeholder="description/hashtags"
          @keydown.enter.stop.prevent="submit()"
        ><br>
        <br>
        set visibility<br>
        <div
          class="visibilityButton"
          @click.prevent.stop="setProperty('private', private?0:1)"
          :class="{'private': private, 'notPrivate': !private}"
          :title="`toggle visibility. (currently: ${private?'NOT':''} featured in public galleries)`"
        ></div><br>
        <button @click="submit()">create</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CollectionTemplate',
  props: [ 'state'],
  data(){
    return {
      name: '',
      description: '',
      slugs: [],
      //originalSlugs: [],
      private: 0,
    }
  },
  methods: {
    setProperty(property, value){
      this[property] = value
    },
    submit(){
      if(!this.name) return
      let obj = {
        name: this.name,
        description: this.description,
        slugs: this.slugs,
        //originalSlugs: this.originalSlugs,
        private: this.private,
      }
      this.state.createCollection(obj)
      this.state.showCollectionTemplate = false
    },
    close(){
      this.state.showCollectionTemplate = false
    }
  },
  mounted(){
    this.$nextTick(()=>{
      this.$refs.name.focus()
    })
  }
}
</script>

<style scoped>
  .collectionTemplate{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 100vh;
    font-size: 14px;
  }
  .collectionTemplateInner{
    text-align: center;
    padding: 25px;
    width: 100%;
    height: 100%;
    font-size: 25px;
    box-sizing: border-box;
    word-break: break-all;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
    background: #201d ;
    word-break: auto-phrase;
  }
  .newCollectionForm{
    border-radius: 6px;
    margin-top: 50px;
    background: #40f4;
    color: #fff;
    font-size: 16px;
    text-align: center;
    width: 500px;
    display: inline-block;
    padding: 20px;
  }
  .collectionFormInput{
    font-family: Courier Prime;
    color: #fff;
    background: #000;
    border: 5px solid #f004;
    font-size: 24px;
    text-align: center;
    margin: 5px;
    width: calc(100% - 60px);
  }
  .visibilityButton{
    width: 120px;
    height: 120px;
    background-size: 120px 120px;
  }
</style>
