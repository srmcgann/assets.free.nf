<template>
  <div class="collectionSelection" v-if="someSelected">
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="toggleShowCollection()"
      class="collectionsButton"
      style="background: #84fd; color: #fff;"
      title="include or exclude this link from collections"
      v-html="showCollection?'close':'assign'"
      v-if="!!state.collections.length"
    >
    </button>
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="manageCollections()"
      class="collectionsButton"
      style="background: #4f8d"
      title="view and edit your collections"
      v-html="state.collections.length ? 'manage' : 'create a collection'"
    ></button><br>
    <div
      @mousedown.stop.prevent
      ref="collectionList"
      class="collectionList"
      :style="`height: ${colHeight}`"
      :class="{'show': showCollection, 'hide': !showCollection}"
    >
      <div v-for="collection in state.collections" style="position: relative;">
        <label
          class="checkboxLabel collectionLabel"
        >
          <input
            :checked="checked(collection)"
            type="checkbox"
            @change="updateSelection($event, collection)"
          >
          <span class="checkmark" style="margin-left: -30px;"></span>
          <span
            class="collectionName"
            style="margin-left: 32px; font-size: 16px;"
             v-html="state.shortText(collection.name, 28)"
          </span>
          {{supplemental(collection)}}
        </label>
        <button
          v-if="mode!='multi' && checked(collection)"
          class="toolbarButtons collectionsButton"
          style="color: #fff; z-index:1000; min-width: unset; height: 27px; background: #84fd;margin: unset;margin-top: -1px; margin-right:5px; padding: 0;font-size:32px;padding-top:7px;position: absolute; margin-left: 28px;"
          @click.stop.prevent="this.state.viewCollection(collection, links.slug)"
          title="view this item, in this collection"
        >👁</button>
      </div>
    </div>
  </div>
  <div v-else class="collectionSelection">
    nothing selected 
  </div>
</template>

<script>

export default {
  name: 'CollectionSelection',
    // "links" prop might receive an array of links (mode == 'multi'),
    //  or a single link (no array, mode != 'multi')
  props: [ 'state', 'links', 'mode', 'someSelected' ],
  components: {
  },
  data(){
    return {
      showCollection: false,
    }
  },
  computed:{
    filteredCollections(){
      let ret = ['none']
      ret = [...ret, ...this.state.collections]
      return ret
    },
    colHeight(){
      return Math.min(200, this.state.collections.length*28+5) + 'px'
    }
  },
  methods: {
    supplemental(collection){
      let ct=0
      this.state.links.map(link=>{
        if(link.selected && collection.meta.slugs.filter(slug=>slug==link.slug).length) ct++
      })
      this.state.userLinks.map(link=>{
        if(link.selected && collection.meta.slugs.filter(slug=>slug==link.slug).length) ct++
      })
      return this.mode == 'multi' ? `[${ct}]` : ''
    },
    checked(collection){
      switch(this.mode){
        case 'multi':
          let checked = false
          this.links.map(v=>{
            if(!!collection.meta.slugs.filter(q=>q==v.slug).length) checked = true
          })
          return checked
        break
        default:
          return !!collection.meta.slugs.filter(v=>v==this.links.slug).length
        break
      }
    },
    manageCollections(){
      this.state.closePrompts()
      this.state.showCollections = true
    },
    toggleShowCollection(){
      this.state.doMouseDown()
      if(!this.showCollection){
        this.$nextTick(()=>{
          this.$nextTick(()=>{
            this.showCollection = !this.showCollection
          })
        })
      }
    },
    pushUpdate(collection){
      let obj = {
        name: collection.name,
        id: collection.id,
        description: collection.meta.description,
        slugs: collection.meta.slugs,
        private: collection.meta.private,
      }
      this.state.updateCollection(obj)
    },
    updateSelection(e, collection){
      let val = e.target.checked
      switch(this.mode){
        case 'multi':
          this.links.map(link=>{
            collection.meta.slugs = collection.meta.slugs.filter(slug=>{
              return slug !== link.slug
            })
            if(val){
              collection.meta.slugs.push(link.slug)
            }
            this.pushUpdate(collection)
          })
        break
        default:
          collection.meta.slugs = collection.meta.slugs.filter(slug=>{
            return slug !== this.links.slug
          })
          if(val){
            collection.meta.slugs.push(this.links.slug)
          }
          this.pushUpdate(collection)
        break
      }
    }
  },
  mounted(){
  },
  watch:{
    'state.click'(val){
      if(val) this.showCollection = false
    }
  }
}
</script>

<style scoped>
  .collectionSelection{
    display: inline-block;
    margin-left: 10px;
  }
  .show{
    display: inline-block;
  }
  .hide{
    display: none;
  }
  .collectionList{
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
  }
  .collectionLabel:hover{
    background: #0f44;
  }
  .collectionLabel{
    display: block;
  }
  .collectionName{
    font-size:14px;
    display:block;
    width: 261px;
    overflow: hidden;
    text-wrap: nowrap;
    color:#4f8;
    padding:0;
    margin-left: -10px;
    padding-left: 10px;
  }
  .checkboxLabel{
    padding-left: unset;
    font-size: 16px;
  }
  input[type=checkbox]{
    margin: 4px;
    margin-right: 0;
  }
</style>
