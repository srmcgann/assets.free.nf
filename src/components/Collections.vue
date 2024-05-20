<template>
  <button
    @click="close()"
    class="cancelButton"
    title="close this view [ESC]"
  >
    close/cancel
  </button>
  <div class="collections" ref="collections" tabindex="1000">
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
          <th>
            <button
              :class="{'sortCol': sortMode=='name'}"
              @click="setSortMode('name')"
              v-html="`name<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='slug'}"
              @click="setSortMode('slug')"
              v-html="`slug<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='views'}"
              @click="setSortMode('views')"
              v-html="`views<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='description'}"
              @click="setSortMode('description')"
              v-html="`description<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='age'}"
              @click="setSortMode('age')"
              v-html="`age<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='created'}"
              @click="setSortMode('created')"
              v-html="`created<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>
            <button
              :class="{'sortCol': sortMode=='items'}"
              @click="setSortMode('items')"
              v-html="`items<br>${sortDir ? '&#8679;' : '&#8681;'}`"
            ></button>
          </th>
          <th>collection<br>tools</th>
        </tr>
        <tr v-for="idx in sortedArray">
          <td v-html="state.collections[idx].name"></td>
          <td v-html="state.collections[idx].slug"></td>
          <td v-html="state.collections[idx].meta.views"></td>
          <td v-html="state.linkify(state.collections[idx].meta.description)"></td>
          <td v-html="state.age(state.collections[idx].meta)"></td>
          <td v-html="state.prettyDate(state.collections[idx].meta)"></td>
          <td>
            <font style="font-size:1em;">{{state.collections[idx].meta.slugs.length}} items</font><br>
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
                class="specialToolButton"
                @click.prevent.stop="state.setCollectionProperty(collection, 'private', state.collections[idx].meta.private?0:1)"
                :class="{'private': state.collections[idx].meta.private, 'notPrivate': !state.collections[idx].meta.private}"
                :title="`toggle visibility. (currently: ${state.collections[idx].meta.private?'NOT':''} featured in public galleries)`"
              ></div>
              <div
                class="copyLinkButton"
                @click.prevent.stop="state.copyLink('col/' + state.collections[idx].slug + '/view')"
                title="copy link to clipboard"
              ></div>
              <a
                :href="state.URLbase + '/col/' + state.collections[idx].slug + '/view'"
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
  data(){
    return {
      sortDir: true,
      sortMode: 'age',
      array: JSON.parse(JSON.stringify(this.state.collections))
    }
  },
  methods: {
    close(){
      this.state.closePrompts()
    },
    setSortMode(mode){
      switch(mode){
        case 'name':
          if(this.sortMode == 'name'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'name'
          }
        break
        case 'slug':
          if(this.sortMode == 'slug'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'slug'
          }
        break
        case 'views':
          if(this.sortMode == 'views'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'views'
          }
        break
        case 'description':
          if(this.sortMode == 'description'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'description'
          }
        break
        case 'age':
          if(this.sortMode == 'age'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'age'
          }
        break
        case 'created':
          if(this.sortMode == 'created'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'created'
          }
        break
        case 'items':
          if(this.sortMode == 'items'){
            this.sortDir = !this.sortDir
          } else {
            this.sortMode = 'items'
          }
        break
      }
    },
    view(collection){
      this.close()
      this.$nextTick(()=>{
        this.state.previewPosition = 0
        this.state.viewCollection(collection, 0)
      })
    }
  },
  computed: {
    sortedByItems(){
      if(this.state.collections){
        let ids = Array(this.state.collections.length).fill().map((v, idx) => {
          return {idx, items: v.slugs.length}
        })
        ids.sort((a,b)=>(this.sortDir?b:a).items-(this.sortDir?a:b).items)
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByViews(){
      if(this.state.collections){
        let ids = Array(this.state.collections.length).fill().map((v, idx) => {
          return {idx, views: v.views}
        })
        ids.sort((a,b)=>(this.sortDir?b:a).views-(this.sortDir?a:b).views)
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByCreated(){
      if(this.state.collections){
        let ids = Array(this.state.collections.length).fill().map((v, idx) => {
          return {idx, created: this.state.prettyDate(v.meta)}
        })
        ids.sort((a,b)=>{
          if((this.sortDir?b:a).created == (this.sortDir?a:b).created) return 0
          if((this.sortDir?b:a).created  < (this.sortDir?a:b).created) return -1
          if((this.sortDir?b:a).created  > (this.sortDir?a:b).created) return 1
        })
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByName(){
      if(this.state.collections){
        let ids = Array(this.state.collections.length).fill().map((v, idx) => {
          return {idx, name: v.name}
        })
        ids.sort((a,b)=>{
          if((this.sortDir?b:a).name == (this.sortDir?a:b).name) return 0
          if((this.sortDir?b:a).name  < (this.sortDir?a:b).name) return -1
          if((this.sortDir?b:a).name  > (this.sortDir?a:b).name) return 1
        })
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedBySlug(){
      if(this.state.collections){
        let ids = Array(this.state.collections.length).fill().map((v, idx) => {
          return {idx, slug: v.slug}
        })
        ids.sort((a,b)=>{
          if((this.sortDir?b:a).slug == (this.sortDir?a:b).slug) return 0
          if((this.sortDir?b:a).slug  < (this.sortDir?a:b).slug) return -1
          if((this.sortDir?b:a).slug  > (this.sortDir?a:b).slug) return 1
        })
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedByDescription(){
      if(this.state.collections){
        let ids = Array(this.state.collections.length).fill().map((v, idx) => {
          return {idx, description: v.description}
        })
        ids.sort((a,b) => {
          if((this.sortDir?b:a).description == (this.sortDir?a:b).description) return 0
          if((this.sortDir?b:a).description  < (this.sortDir?a:b).description) return -1
          if((this.sortDir?b:a).description  > (this.sortDir?a:b).description) return 1
        })
        return ids.map(v=>v.idx)
      }else{
        return []
      }
    },
    sortedArray(){
      switch(this.sortMode){
        case 'name'          : return this.sortedByNames; break
        case 'slug'          : return this.sortedBySlug; break
        case 'views'         : return this.sortedByViews; break
        case 'description'   : return this.sortedByDescription; break
        case 'age'           : return this.sortedByAge; break
        case 'created'       : return this.sortedByCreated; break
        case 'items'         : return this.sortedByItems; break
      }
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
  button{
    min-width: unset;
    font-size: 14px;
  }
</style>
