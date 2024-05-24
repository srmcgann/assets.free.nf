<!--
todo

  ✔ upload targets @ not-logged-in
  ✔ dropable files
  ✔ fix video & audio thumbnails
  ✔ file hash unifies same-upload sources
  ✔ fix loading animations
  ✔ preload loading (and other) assets
  ✔ display errors
  ✔ tile-able cards (flex)
  ✔ log original file name
  ✔ lightbox sim / previews
  ✔ checkboxes & "with selected" toobar
  ✔ uploading progress bars
  ✔ upload progress bars
  ✔ link tool buttons on preview modal
  ✔ URL uploads
  ✔ log & display asset origin
  ✔ anonymous uploads are autodeleted after 24hrs
  ✔ admin panel (auto-available to logged in admins)
  ✔ random Link loading function (for general purposes, including viewing of collections)
  ✔ clicking anywhere hides collection selection lists
  ✔ scrollable edit-collection view
  ✔ no collections -> membership button becomes 'create collection'
  ✔ add 'last seen' update to login
  ✔ link cache for all links loaded in current session
  ✔ adding/deleting items refactors pagination, where appropriate
  ✔ link tools in collections view are collection tools
  ✔ menu item: w/selected -> add to collection
  ✔ slideshow queues appropriate next/prev items, depending on mode (curpage vs collection etc)
  ✔ random-slugify collections
  ✔ make asset "name" fields editable
  ✔ deleting asset also removes it from all connected collections
  ✔ sortable 'stats overview' page for users
  ✔ add items/page selection to user prefs
  ✔ uploading progress animations & color change @ 100%
  ✔ configurable default # of comments per asset
  ✔ load user info for all comments, as received
  ✔ single asset view / URL (same /uploads/... URL, minus the file suffix? [no! /item/ kthx])
  ✔ "pinning/unpinning" asset data & tools @ preview [no-fade option]
  ✔ linkfy comments & descriptions
  ✔ magnifying glass tool @ previews
  ✔ URL/mode for 'user' (added to 'default', 'col', 'item')
  ✔ featured items, & tool for browsing user content by admins, to add to featured items
  ✔ make columns in admin view sortable
  ✔ "trending/popular" page
  ✔ add sortability to collections list columns
  ✔ download entire catalog as a zip file
  ✔ download selected as a zip file
  ✔ URLs for all "screens", including collections, with back-button functionality
    └-> ✔ default
    └-> ✔ collections
    └-> ✔  asset management/tools
           └->  ✔ collections
           └->  ✔ stats/overview (when made)
           └->  ✔ comments (when made)
  * users, optional logins/profiles
    └-> ✔ login button
    └-> ✔ profile page
    └-> ✔ collections / share-ability
    └-> ✔ comments
    └-> ✔ votes
        
  * search (title/description/user/comments[optional])
  * social media metadata
  * load/resource balancing
  * admin data -> add 'owner' & 'number of owners' for disk assets
  * youtube field  // likely not to work without shell access
  
-->

<template>
  <div
    class="link"
    ref="anchor"
  >
    <label v-if="!omitAssetData && state.loggedIn" class="checkboxLabel" :key="link.linkType+link.ct+'key'">
      <input type="checkbox" v-model="link.selected" @input="updateLinkSelected()">
      <span class="checkmark" style="margin-left: -30px;"></span>
      <span style="font-size:.75em;margin-top:5px;display:block;color:#4f8;padding:0;margin-left:-34px;">select</span><br>
    </label>
    
    <div class="views" v-html="state.views(link)">
    </div>
    
    <div
      @mousedown.stop.prevent
      class="linkThumb"
      ref="linkThumb"
      @click.prevent.stop="state.preview(link)"
      title="view this asset"
    ></div>
    <!--#{{link.ct+1}}-->
    <div class="linkButtons" @mousedown.stop.prevent @click.stop.prevent>
      <div
        class="specialToolButton"
        @click.prevent.stop="state.setLinkProperty(link, 'private', link.private?0:1)"
        :class="{'private': link.private, 'notPrivate': !link.private}"
        :title="`toggle visibility. (currently: ${link.private?'NOT':''} featured in public galleries)`"
        v-if="link.userID == state.loggedinUserID || state.admin"
      ></div>
      <div
        class="copyLinkButton"
        @click.prevent.stop="state.copyLink(link.href)"
        title="copy link to clipboard"
      ></div>
      <a
        :href="state.URLbase + '/' + link.href"
        class="openButton"
        @click.prevent.stop="state.openLink(link)"
        title="open link in new tab"
      ></a>
      <div
        class="downloadButton"
        @click.prevent.stop="state.downloadLink(link, state.fullFileName(link))"
        title="download asset"
      ></div>
      <div
        class="deleteSingleButton"
        @click.prevent.stop="state.deleteSingle(link)"
        title="delete this asset only"
        v-if="link.userID == state.loggedinUserID || state.admin"
      ></div>
    </div>
    <br>
    <AssetData :omitAssetData="omitAssetData" :state="state" :link="link" />
  </div>
</template>

<script>
import AssetData from './AssetData'

export default {
  name: 'Link',
  components: { AssetData },
  props: [ 'state', 'link', 'omitAssetData' ],
  data(){
    return {
      c: document.createElement('canvas'),
      x: null,
      linkType: '',
      img: null,
      w: 0,
      h: 0,
      t: 0,
    }
  },
  computed: {
  },
  methods: {
    updateLinkSelected(){
      if(this.link.selected){
        this.link.selected = false
      }else{
        this.link.selected = true
      }
    },
    getThumb(){
      let l
      try{
        // choose 1
        
        // uncomment for 'views' to increment with every direct asset view,
        // link displayed, and thumb created, even for owner
        /*
        this.img.src = this.linkType == 'audio' ? this.state.URLbase + '/' + 'musicNotes.svg' : this.state.URLbase + '/' + 'thumb.php?res=uploads/' + (this.link.slug + '.' + this.link.href.split('.')[1])
        */
        // uncomment for 'views' to only increment at direct asset views, not /item/ shim views etc
        
        this.img.src = this.linkType == 'audio' ? this.state.URLbase + '/' + 'musicNotes.svg' : this.state.URLbase + '/' + 'thumb.php?res=resources/' + (this.link.originalSlug + '.' + this.link.href.split('.')[1])
        
      } catch(error){
        console.log(error)
      }
    },
    Draw(){
      this.x.globalAlpha = 1
      this.x.fillStyle='#0008'
      this.x.fillRect(0,0,this.c.width,this.c.height)
      let fillStyle = 'contain'
      let scl
      switch(fillStyle){
        case 'contain':
          scl = this.c.width/this.c.height <= 1.777777778 ? this.c.width/this.w : this.c.height/this.h
          break
        case 'cover':
          scl = this.c.width/this.c.height > 1.777777778 ? this.c.width/this.w : this.c.height/this.h
          break
      }
      let margin = 5
      let w = this.w * scl - margin
      let h = this.h * scl - margin
      this.x.drawImage(this.img,this.c.width/2-w/2,this.c.height/2-h/2,w,h)
      this.t += 1/60
      switch(this.linkType){
        case 'image':
          setTimeout(()=>{
            if(!this.img.width || !this.img.height) this.getThumb()
            requestAnimationFrame(this.Draw)
          }, 1000)
          break
        case 'video':
          if(!this.img.videoWidth || !this.img.videoHeight){
            setTimeout(()=>{
              this.getThumb()
              requestAnimationFrame(this.Draw)
            }, 1000)
          }else{
            requestAnimationFrame(this.Draw)
          }
          break
      }
    }
  },
  mounted(){
    this.$refs.linkThumb.appendChild(this.c)
    this.x = this.c.getContext('2d')
    this.linkType = this.link.filetype.split('/')[0]
    this.c.width = 500
    this.c.height = 500/1.77777778
    this.c.style.width = '200px'
    this.c.style.height = '113px'
    this.c.style.borderRadius = '20px'
    if(this.linkType == 'video'){
      this.img = document.createElement('video')
      this.img.loop = true
      this.img.muted = true
      console.log('fresh mount')
      this.img.oncanplay = () => {
        console.log('starting video! beep beep')
        this.w = this.img.videoWidth
        this.h = this.img.videoHeight
        
        this.img.play()
        this.Draw()
      }
      this.img.src = `${this.state.URLbase}/resources/` + this.link.originalSlug + '.' + this.link.href.split('.')[1]
    }
    if(this.linkType == 'image' || this.linkType == 'audio'){
      this.img = new Image()
      this.img.onload = () => {
        this.w = this.img.width
        this.h = this.img.height
        this.Draw()
      }
      this.getThumb()
    }
  }
}
</script>

<style scoped>
  .link:hover{
    background-color: #408a;
  }
  .link{
    display: inline-block;
    color: #acd;
    background-color: #002a;
    font-size: 20px;
    box-sizing: border-box;
    text-align: left;
    margin: 5px;
    word-break: break-word;
    border-radius: 10px;
    vertical-align: top;
    border: 6px solid #50f2;
    padding: 5px;
    width: 370px;
  }
  .href{
    font-size: .6em;
    color: #0ff;
    margin: 5px;
    padding: 3px;
    width: calc(100% - 10px);
  }
  .linkThumb{
    float: left;
    width: 200px;
    height: 113px;
    margin: 5px;
    margin-top: 10px;
    cursor: pointer;
    background-size: contain;
    background-position: 20px 20px;
    background-repeat: no-repeat;
    background-color: #000;
    border-radius: 20px;
  }
  .views{
    position: relative;
    min-width: 124px;
    text-align: right;
    right: 10px;
  }
  .specialToolButton{
    width: 64px;
    height: 45px;
    background-size: 49px 49px;
  }
</style>

