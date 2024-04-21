<template>
  <table class="assetData">
    <tr v-if="state.showPreview"><td class="tdLeft">views</td><td class="tdRight" v-html="state.views(link)"></td></tr>
    <tr>
      <td class="tdLeft">
        slug
      </td>
      <td class="tdRight">
        <a
          :href="state.URLbase + '/' + link.href"
          target="_blank"
          title="open link in new tab"
          v-html="link.slug"
          class="assetDataSlug"
        >
        </a>
      </td>
    </tr>
    <tr>
      <td
        class="tdLeft">name</td><td class="tdRight"
        v-if="state.loggedinUserID == link.userID"
      >
        <input
          type="text"
          v-model="link.name"
          class="assetNameInput"
          @input="state.updateLinkProp(link, 'name')"
          @click.prevent.stop="selectText(this)"
        >
      </td>
      <td v-else v-html="state.fileName(link.name)"></td>
    </tr>
    <tr><td class="tdLeft">uploaded</td><td class="tdRight" v-html="state.prettyDate(link)"></td></tr>
    <tr><td class="tdLeft">age</td><td class="tdRight" v-html="state.age(link)"></td></tr>
    <tr><td class="tdLeft">size</td><td class="tdRight" v-html="state.size(link.size)"></td></tr>
    <tr v-if="link.userID == state.loggedinUserID || state.admin">
      <td class="tdLeft">collections</td><td class="tdRight">
        <CollectionSelection :state="state" :links="link" :mode="default" :someSelected="true"/>
      </td>
    </tr>
    <tr><td class="tdLeft">id</td><td class="tdRight" v-html="link.id"></td></tr>
    <tr><td class="tdLeft">origin</td><td class="tdRight" v-html="link.origin.split(':')[0]"></td></tr>
    <!-- <tr><td class="tdLeft">first seen</td><td class="tdRight"v-html="state.firstSeen(link)"></td></tr> --> 
  </table>
</template>

<script>
import CollectionSelection from './CollectionSelection.vue'

export default {
  name: 'AssetData',
  props: [ 'state', 'link' ],
  components: {
    CollectionSelection
  },
  data(){
    return {
    }
  },
  computed:{
  },
  methods: {
    selectText(el){
      console.log(el)
      el.focus()
      el.select()
    }
  },
  mounted(){
  }
}
</script>

<style scoped>
  .assetData{
    border-collapse: collapse;
    font-size: 14px;
    text-shadow: 2px 2px 2px #000;
    background: #0003;
    width: 100%;
  }
  .assetDataSlug{
    background-color: #08f;
    text-shadow: 1px 1px 2px #000;
    color: #fff;
    border-radius: 5px;
    min-width: 85px;
    display: inline-block;
    background-image: url(../assets/open.png);
    background-position: 85px 3px;
    background-repeat: no-repeat;
    background-size: 10px 10px;
    padding-left: 10px;
    padding-right: 10px;
  }
  .assetNameInput{
    max-width: 225px;
  }
  .tdLeft{
    width: 100px;
    max-width: unset;
  }
</style>
