<template>
  <div class="collections" ref="collections" tabindex="1000">
    <button
      @click="close()"
      class="cancelButton"
      title="close this view [ESC]"
    >
      close/cancel
    </button>
    <div class="collectionsInner">
      &lt;&lt;&lt;  COLLECTIONS  >>><br>
      <button
        v-if="state.collections.length"
        @click="state.showCollectionTemplate=true"
        title="create new collection"
        class="addButton"
      >
      +
      </button>
      <table class="collectionsTable" v-if="state.collections.length">
        <tr>
          <th>name</th>
          <th>views</th>
          <th>description</th>
          <th>age</th>
          <th>created</th>
          <th>items</th>
          <th>collection<br>tools</th>
        </tr>
        <tr v-for="collection in state.collections">
          <td v-html="collection.name"></td>
          <td v-html="collection.meta.views"></td>
          <td v-html="collection.meta.description"></td>
          <td v-html="state.age(collection.meta)"></td>
          <td v-html="state.prettyDate(collection.meta)"></td>
          <td>
            <font style="font-size:1em;">{{collection.meta.slugs.length}} items</font><br>
            <button
              @click="view(collection)"
              class="viewCollectionButton"
              title="view this collection"
            >view</button><br>
            <button
              @click="state.showEditCollection(collection)"
              class="editCollectionButton"
              title="edit this collection"
            >edit</button>
          </td>
          <td>
            <div class="linkButtons">
              <div
                class="visibilityButton"
                @click.prevent.stop="state.setCollectionProperty(collection, 'private', collection.meta.private?0:1)"
                :class="{'private': collection.meta.private, 'notPrivate': !collection.meta.private}"
                :title="`toggle visibility. (currently: ${collection.meta.private?'NOT':''} featured in public galleries)`"
              ></div>
              <div
                class="copyLinkButton"
                @click.prevent.stop="state.copyLink('col/' + collection.slug + '/view')"
                title="copy link to clipboard"
              ></div>
              <a
                :href="state.URLbase + '/col/' + collection.slug + '/view'"
                class="openButton"
                @click.prevent.stop="state.openCollection(collection)"
                title="open link in new tab"
              ></a>
              <!-- <div
                class="downloadButton"
                @click.prevent.stop="state.downloadLink(link, state.fullFileName(link))"
                title="download asset"
              ></div> -->
              <div
                class="deleteSingleButton"
                @click.prevent.stop="state.deleteCollection(collection)"
                title="delete this collection"
              ></div>
            </div>
          </td>
        </tr>
      </table>
      <CollectionTemplate  v-else :state="state" />
    </div>
  </div>
</template>

<script>
import CollectionTemplate from './CollectionTemplate.vue'

export default {
  name: 'Collections',
  props: [ 'state' ],
  components:{
    CollectionTemplate,
  },
  methods: {
    close(){
      this.state.closePrompts()
    },
    view(collection){
      this.close()
      this.$nextTick(()=>{
        this.viewCollection(collection)
      })
    }
  },
  mounted(){
    this.$refs.collections.focus()
    this.state.fetchCollections(this.state.loggedinUserID)
  }
}
</script>

<style scoped>
  .collections{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 100vh;
    font-size: 14px;
  }
  .collectionsInner{
    text-align: center;
    padding: 25px;
    width: 100%;
    height: 100%;
    font-size: 25px;
    box-sizing: border-box;
    word-break: break-all;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
    background: #102d ;
    word-break: auto-phrase;
    overflow: auto;
  }
  tr{
    background: #123d;
  }
  th{
    padding-left: 10px;
    padding-right: 10px;
    border: 1px solid #0ff1;
    border-bottom: 20px solid #0ff1;
    color: #4fc;
    font-size: 16px;
  }
  td{
    font-size: 14px;
    padding-left: 10px;
    padding-right: 10px;
    border: 1px solid #0ff1;
    border-bottom: 20px solid #0ff1;
  }
  .collectionsTable{
    position: relative;
    left: 50%;
    transform: translate(-50%);
    border-collapse: collapse;
    width: calc(100% - 100px);
  }
  .addButton{
    background: #0f8;
    padding: 0;
    font-size: 50px;
    padding-top: 6px;
    line-height: 30px;
    border-radius: 5px;
    margin: 6px;
  }
  
</style>
