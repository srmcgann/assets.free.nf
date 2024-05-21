<template>
  <button
    @click="close()"
    class="cancelButton"
    title="close this view [ESC]"
  >
    close/cancel
  </button>
  <div class="editCollection" ref="editCollection" tabindex="2000">
    <div class="editCollectionInner">
      <br>
      EDIT COLLECTION
      <br><br>
      changes are effective instantly, universe-wide<br>
      <div class="editCollectionForm">
        name<br>
        <input
          class="collectionFormInput"
          ref="name"
          v-model="collection.name"
          placeholder="collection name"
          @input.stop.prevent="save()"
          @keyup.enter.prevent="close()"
        ><br><br>
        description<br>
        <input
          class="collectionFormInput"
          v-model="collection.meta.description"
          placeholder="description/hashtags"
          @input.stop.prevent="save()"
          @keyup.enter.prevent="close()"
        ><br>
        <br>
        set visibility<br>
        <div
          class="specialToolButton"
          @click.prevent.stop="setProperty('private', collection.meta.private?0:1)"
          :class="{'private': collection.meta.private, 'notPrivate': !collection.meta.private}"
          :title="`toggle visibility. (currently: ${collection.meta.private?'NOT':''} featured in public galleries)`"
        ></div><br>
        <div
          style="display: flex; width: 100%; flex-wrap: wrap; justify-content: space-evenly;"
          v-if="collection.meta.slugs.length">
          <Link
            v-for="link in state.miscLinks"
            :omitAssetData="false"
            :key="link.id"
            :state="state" :link="link" />
        </div>
        <div v-else>
          <br><br>
          [nothing added yet - go browse!]
        </div>
        <!-- <button @click="save()">save</button> -->
      </div>
    </div>
  </div>
</template>

<script>
import Link from './Link'

export default {
  name: 'EditCollection',
  props: [ 'state', 'collection'],
  components:{
    Link
  },
  data(){
    return {
    }
  },
  computed:{
  },
  methods: {
    setProperty(property, value){
      this.collection.meta[property] = value
      this.save()
    },
    save(){
      let obj = {
        name: this.collection.name,
        id: this.collection.id,
        description: this.collection.meta.description,
        slugs: this.collection.meta.slugs,
        private: this.collection.meta.private,
      }
      this.state.updateCollection(obj)
    },
    close(){
      this.state.editCollection = []
    }
  },
  mounted(){
    this.$nextTick(()=>{
      this.$refs.name.focus()
    })
    this.collection.meta.slugs = this.collection.meta.slugs.filter(v=>v)
    this.state.loadLinks(this.collection.meta.slugs, false)
  }
}
</script>

<style scoped>
  .editCollection{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    background: #201d ;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    font-size: 14px;
  }
  .editCollectionInner{
    text-align: center;
    padding: 25px;
    width: 100%;
    height: 100%;
    font-size: 25px;
    box-sizing: border-box;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
    word-break: auto-phrase;
  }
  .editCollectionForm{
    border-radius: 6px;
    margin-top: 50px;
    margin-bottom: 50px;
    background: #40f4;
    color: #fff;
    font-size: 16px;
    text-align: center;
    width: 100%;
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
    max-width: 600px;
  }
  .specialToolButton{
    width: 120px;
    height: 120px;
    background-size: 120px 120px;
  }
</style>
