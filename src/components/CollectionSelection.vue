<template>
  <div class="collectionSelection" v-if="someSelected">
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="toggleShowCollection()"
      class="collectionsButton"
      style="background: #84fd"
      v-html="showCollection?'close':'assign'"
      v-if="!!state.collections.length"
    >
    </button>
    <button
      @mousedown.stop.prevent
      @click.stop.prevent="manageCollections()"
      class="collectionsButton"
      style="background: #4f8d"
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
          @mousedown.stop.prevent
        >
          <input
            :checked="checked(collection)"
            type="checkbox"
            @mousedown.stop.prevent
            @change="updateSelection($event, collection)"
          >
          <span class="checkmark" style="margin-left: -30px;"></span>
          <span class="collectionName">
            {{state.shortText(collection.name, 18)}}
            {{supplemental(collection)}}
          </span>
        </label><br>
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
        if(collection.meta.slugs.filter(slug=>slug==link.slug).length) ct++
      })
      this.state.userLinks.map(link=>{
        if(collection.meta.slugs.filter(slug=>slug==link.slug).length) ct++
      })
      return this.mode == 'multi' ? `(${ct} items)` : ''
    },
    checked(collection){
      switch(this.mode){
        case 'multi':
          let checked = false
          console.log('check', this.links)
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
    width: 215px;
    background: #123;
    line-height: 28px;
    margin-top: 5px;
    padding-left: 2px;
    padding-top: 2px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #0ff4;
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
    width: 161px;
    overflow: hidden;
    text-wrap: nowrap;
    color:#4f88;
    padding:0;
    margin-left: -10px;
    padding-left: 10px;
  }
  .collectionsButton{
    line-height: 13px;
    font-size: 16px;
    height: 24px;
    margin: 0;
    margin-right: 10px;
    min-width: unset;
    width: 
  }
  .checkboxLabel{
    padding-left: unset;
  }
  input[type=checkbox]{
    margin: 4px;
    margin-right: 0;
  }
</style>
