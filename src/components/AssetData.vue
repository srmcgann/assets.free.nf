<template>
  <table class="assetData">
    <tr v-if="state.showPreview"><td class="tdLeft">views</td><td class="tdRight" v-html="state.views(link)"></td></tr>
    <tr>
      <td class="tdLeft">
        slug
      </td>
      <td class="tdRight">
        <a
          @mousedown.stop.prevent
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
          @mousedown.stop
          @input="state.setLinkProperty(link, 'name', link.name.trim(), false, true)"
          @click.prevent.stop="$event.target.select()"
        >
      </td>
      <td
        v-else
        class="tdRight"
        v-html="state.fileName(link)"
      ></td>
    </tr>
    <tr>
      <td
        class="tdLeft">description</td><td class="tdRight"
        v-if="state.loggedinUserID == link.userID"
      >
        <input
          type="text"
          v-model="link.description"
          class="assetNameInput"
          @mousedown.stop
          @input="state.setLinkProperty(link, 'description', link.description, false, true)"
          @click.prevent.stop="$event.target.select()"
        >
      </td>
      <td
        v-else
        class="tdRight"
        v-html="state.shortText(link.description, 16)"
      ></td>
    </tr>

    <tr v-if="link.upvotes">
      <td class="tdLeft">upvotes</td><td class="tdRight" v-html="link.upvotes"></td>
    </tr>
    <tr v-if="link.downvotes">
      <td class="tdLeft">downvotes</td><td class="tdRight" v-html="link.downvotes"></td>
    </tr>
    <tr>
      <td class="tdLeft">hash</td>
      <td
        class="tdRight"
        v-html="link.hash"
        style="font-size:11px;"
      ></td>
    </tr>
    <tr><td class="tdLeft">filetype</td><td class="tdRight" v-html="link.filetype"></td></tr>
    <tr><td class="tdLeft">owner ID</td><td class="tdRight" v-html="link.userID"></td></tr>

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
    font-size: 14px;
    background: #000;
    border-bottom: 1px solid #4f84;
  }
  .tdLeft{
    width: 100px;
    max-width: unset;
  }
</style>
