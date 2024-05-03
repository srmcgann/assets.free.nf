<template>
  <table class="assetData">
    <tr v-if="state.showPreview"><td class="tdLeft">views</td><td class="tdRight" v-html="state.views(link)"></td></tr>
    <tr>
      <td class="tdLeft">
        <button
          @click.stop.prevent="link.expandedInfo = !link.expandedInfo"
          v-if="!omitAssetData"
          class="expandInfoButton"
          title="show less-common/extra details about this asset"
          v-html="link.expandedInfo ? 'less' : 'more'"
        >
        </button>
        owner
      </td>
      <td class="tdRight">
        <div class="loggedIn" style="display: inline-block; float: left;">
          <div
            @click="state.getUserStats(link.userID)"
            class="avatar"
            :title="`this asset belongs to ${state.userInfo[link.userID]?.name}`"
            :style="`background-image: url(${avatar})`"
          ></div>
        </div>
        <div style="display: inline-block; float: left;color: #fff;text-shadow: 0 0 3px #40f;margin: 10px;font-size: 16px;">
          {{state.shortText(state.userInfo[link.userID]?.name, 16)}}
        </div>
      </td>
    </tr>
    <tr v-if="!omitAssetData">
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
          tabindex="0"
          @focus="onfocus()"
          @blur="onblur()"
          @mousedown.stop
          @input="state.setLinkProperty(link, 'name', link.name, false, true)"
          @click.prevent.stop="$event.target.select()"
        >
      </td>
      <td
        v-else
        class="tdRight"
        v-html="state.fileName(link)"
      ></td>
    </tr>
    <tr v-if="link.description">
      <td
        class="tdLeft">description</td><td class="tdRight"
        v-if="state.loggedinUserID == link.userID"
      >
        <input
          type="text"
          v-model="link.description"
          class="assetNameInput"
          tabindex="0"
          @focus="onfocus()"
          @blur="onblur()"
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
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">hash</td>
      <td
        class="tdRight"
        v-html="link.hash"
        style="font-size:11px;"
      ></td>
    </tr>
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">filetype</td>
      <td class="tdRight" v-html="link.filetype"></td>
    </tr>
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">owner ID</td>
      <td class="tdRight" v-html="link.userID"></td>
    </tr>

    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">uploaded</td>
      <td class="tdRight" v-html="state.prettyDate(link)"></td>
    </tr>
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">age</td>
      <td class="tdRight" v-html="state.age(link)"></td>
    </tr>
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">size</td>
      <td class="tdRight" v-html="state.size(link.size)"></td>
    </tr>
    <tr v-if="!omitAssetData && link.userID == state.loggedinUserID || state.admin">
      <td class="tdLeft">collections</td><td class="tdRight">
        <CollectionSelection :state="state" :links="link" :mode="default" :someSelected="true"/>
      </td>
    </tr>
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">asset id</td>
      <td class="tdRight" v-html="link.id"></td>
    </tr>
    <tr v-if="!omitAssetData && link.expandedInfo">
      <td class="tdLeft">origin</td>
      <td class="tdRight" v-html="link.origin.split(':')[0]"></td>
    </tr>
    <!-- <tr><td class="tdLeft">first seen</td><td class="tdRight"v-html="state.firstSeen(link)"></td></tr> -->
    <tr style="height: 28px;">
      <td class="tdLeft">
        comments
        <span
          style="font-size:15px;"
          v-html="`(${link.comments.length})`"
        ></span>
      </td>
      <td class="tdRight">
      </td>
    </tr>
  </table>
  <LinkComments :state="state" :link="link" />
</template>

<script>
import CollectionSelection from './CollectionSelection.vue'
import LinkComments from './LinkComments.vue'

export default {
  name: 'AssetData',
  props: [ 'state', 'link', 'omitAssetData' ],
  components: {
    CollectionSelection,
    LinkComments
  },
  data(){
    return {
    }
  },
  computed:{
    avatar(){
      if(this.state.userInfo[this.link.userID]?.avatar.indexOf('avatarDefault.png') != -1){
        return this.state.URLbase + '/avatarDefault.png'
      }else{
        return this.state.userInfo[this.link.userID]?.avatar
      }
    }
  },
  methods: {
    onfocus(){
      console.log('blocking fade')
      this.state.blockFade = true
    },
    onblur(){
      console.log('re-enabling fade')
      this.state.blockFade = false
    }
  },
  mounted(){
    if(typeof this.link.expandedInfo == 'undefined'){
      this.link.expandedInfo = false
    }
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
    max-width: unset;
    min-width: 105px;
  }
</style>
