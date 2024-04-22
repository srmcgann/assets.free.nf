<template>
  <div>
    <Header              :state="state" v-if="!popupVisible" />
    <Toolbar             :state="state" v-if="!popupVisible" />
    <Main                :state="state" v-if="state.mode && !popupVisible" />
    <Footer              :state="state" v-if="!popupVisible" />
    <UserSettings        :state="state" v-if="state.userSettingsVisible" />
    <LoginPrompt         :state="state" v-if="state.showLoginPrompt" />
    <Admin               :state="state" v-if="showAdminButton" />
    <Collections         :state="state" v-if="state.showCollections" />
    <EditCollection      :state="state" v-if="state.editCollection.length"
                         :collection="state.editCollection[0]" />
    <CollectionTemplate  :state="state" v-if="state.showCollectionTemplate" />
    <Modal
      :state="state"
      v-if="state.showModal"
      :content="state.modalContent"
    />
    <Loading
      :state="state"
      v-if="state.showLoading"
    />
    <Preview
      :state="state"
      v-if="state.showPreview"
      :link="state.previewLink"
    />
    <div id="copyConfirmation"><div id="innerCopied">COPIED!</div></div>
  </div>
</template>

<script>
import Main from './components/Main'
import Modal from './components/Modal'
import Admin from './components/Admin'
import Header from './components/Header'
import Footer from './components/Footer'
import Preview from './components/Preview'
import Toolbar from './components/Toolbar'
import Loading from './components/Loading'
import Collections from './components/Collections'
import LoginPrompt from './components/LoginPrompt'
import UserSettings from './components/UserSettings'
import EditCollection from './components/EditCollection'
import CollectionTemplate from './components/CollectionTemplate'

export default {
  name: 'App',
  components: {
    Main,
    Modal,
    Admin,
    Footer,
    Header,
    Toolbar,
    Loading,
    Preview,
    LoginPrompt,
    Collections,
    UserSettings,
    EditCollection,
    CollectionTemplate,
  },
  data(){
    return {
      state: {
        footerMsg: '<b><span style="transform: scaleX(3.325);display: inline-block; margin-left: 86px; ">ASSETS</span></b><br>A FREE DIGITAL ASSET<br><span style="transform: scaleX(.87);display: inline-block; margin-left: -18px;">HOSTING SERVICE - ©'+(new Date()).getFullYear() + `</span><br><a href="mailto:whitehotrobot@gmail.com"><span style="transform: scaleX(.87);display: inline-block; margin-left: -18px;">whitehotrobot@gmail.com</span></a>`,
        links: [],         // immediate uploads
        userLinks: [],     // logged-in user - current pg only
        miscLinks: [],     // alt views, e.g. per-collection links
        cacheLinks: [],    // set of links seen in current session
        uploadInProgress: false,
        showModal: false,
        fetchUserLinks: null,
        setCookie: null,
        mode: null,
        age: null,
        views: null,
        size: null,
        deleteSelected: null,
        showEditCollection: null,
        editCollection: [],
        multipleLinks: null,
        getAvatar: null,
        showLoading: false,
        showPreview: false,
        rootDomain: location.hostname,
        modalContent: '',
        modalQueue: [],
        previewLink: null,
        passhash: '',
        loggedinUserID: null,
        loadCollection: null,
        fetchCollections: null,
        click: false,
        loadLinks: null,
        closeModal: null,
        closePreview: null,
        showAssetPreview: [],
        showAvatarPreview: [],
        closePrompts: null,
        defaultAvatar: 'avatarDefault.png',
        loggedInUser: {
          avatar: 'avatarDefault.png',
        },
        loggedinUserName: '',
        copyLink: null,
        viewCollection: null,
        deleteSingle: null,
        syncCache: null,
        deleteCollection: null,
        createCollection: null,
        choice: null,
        downloadLink: null,
        next: null,
        prev: null,
        fileName: null,
        updateCollection: null,
        fullFileName: null,
        login: null,
        showCollectionTemplate: false,
        register: null,
        isNumber: null,
        openCollection: null,
        setLinkProperty: null,
        setCollectionProperty: null,
        collections: [],
        setLinkPropertySelected: null,
        URLbase: null,
        linksSelectedTally: 0,
        showCollection: false,  // to view an individual collection
        showCollections: false,  // to invoke user's collection list view
        logout: null,
        onkeydown: null,
        showAdmin: false,
        uploadEventTally: 0,
        fetchEventTally: 0,
        deleteEventTally: 0,
        regusername: '',
        username: '',
        userView: false,
        prettyDate: null,
        firstSeen: null,
        password: '',
        showUserSettings: null,
        uploadFromURL: '',
        invalidLoginAttempt: false,
        displayLoginRequired: false,
        userSettingsVisible: false,
        jumpToPage: null,
        regpassword: '',
        showUploadModal: false,
        loadingAssets: true,
        previewCollection: null,
        loadingCollections: true,
        checkLogin: null,
        adminData: null,
        search: {
          string: '',
          //audiocloudTracks: [],
          hits: 0,
          inProgress: false,
          timer: 0,
          timerHandle: null,
          exact: false,
          allWords: true
        },
        confirmpassword: '',
        doMouseDown: null,
        totalPages: 0,
        totalUserPages: 0,
        setLinksOwner: null,
        curPage: 0,
        shortText: null,
        curUserPage: 0,
        maxResultsPerPage: 6,
        collectionsPage: 0,
        maxCollectionResultsPerPage: 6,
        regressPage: null,
        advancePage: null,
        lastPage: null,
        firstPage: null,
        selectAll: null,
        deSelectAll: null,
        showLoginPrompt: false,
        loadFiles: null,
        showRegister: false,
        loggedIn: false,
        loginPromptVisible: false,
        getAdminData: null,
        addLink: null,
        previewPosition: 0,
        keys: Array(128).fill(false),
      }
    }
  },
  methods:{
    alphaToDec(val){
      let pow=0
      let res=0
      let cur, mul
      while(val!=''){
        cur=val[val.length-1]
        val=val.substring(0,val.length-1)
        mul=cur.charCodeAt(0)<58?cur:cur.charCodeAt(0)-(cur.charCodeAt(0)>96?87:29)
        res+=mul*(62**pow)
        pow++
      }
      return res
    },
    prev(){
      if(!this.state.showPreview) return
      this.state.showPreview = false
      this.state.previewPosition -= 1
      
      switch(this.state.mode){
        case 'default':
          if(this.state.previewPosition<0) this.state.previewPosition = this.state.userLinks.length + this.state.links.length - 1
          if(this.state.previewPosition>this.state.links.length-1){
            this.state.previewLink = this.state.userLinks[this.state.previewPosition - this.state.links.length]
          }else{
            this.state.previewLink = this.state.links[this.state.previewPosition]
          }
        break
        case 'col':
          if(this.state.previewPosition<0) this.state.previewPosition = this.state.miscLinks.length - 1
          if(this.state.previewPosition>this.state.miscLinks.length-1){
            this.state.previewLink = this.state.miscLinks[this.state.previewPosition - this.state.miscLinks.length]
          }else{
            this.state.previewLink = this.state.miscLinks[this.state.previewPosition]
          }
        break
      }
      
      this.$nextTick(()=>{
        this.state.showPreview = true
      })
    },
    next(){
      if(!this.state.showPreview) return
      this.state.showPreview = false
      this.state.previewPosition++
      switch(this.state.mode){
        case 'default':
          this.state.previewPosition %= this.state.userLinks.length + this.state.links.length
          if(this.state.previewPosition>this.state.links.length-1){
            this.state.previewLink = this.state.userLinks[this.state.previewPosition - this.state.links.length]
          }else{
            this.state.previewLink = this.state.links[this.state.previewPosition]
          }
        break
        case 'col':
          this.state.previewPosition %= this.state.miscLinks.length
          if(this.state.previewPosition>this.state.miscLinks.length-1){
            this.state.previewLink = this.state.miscLinks[this.state.previewPosition - this.state.miscLinks.length]
          }else{
            this.state.previewLink = this.state.miscLinks[this.state.previewPosition]
          }
        break
      }
      this.$nextTick(()=>{
        this.state.showPreview = true
      })
    },
    register(){
      console.log('registering')
      this.state.showLoginPrompt = true
      this.state.showRegister = true
    },
    showUserSettings(){
      document.getElementsByTagName('HTML')[0].style.overflowY = 'hidden'
      this.state.userSettingsVisible = true
    },
    viewCollection(collection){
      this.state.mode = 'col'
      this.state.previewCollection = collection
      console.log('loading collection', collection)
      this.state.loadLinks(collection.meta.slugs, true)
      history.pushState(null,null,this.URLbase + `/col/${collection.slug}/view`) //+ (this.state.curPage + 1))
    },
    firstPage(){
      let search = this.state.search.string ? ('/1/' + (this.state.search.string)) : ''
      switch(this.state.mode){
        case 'u':
          window.location.href = this.URLbase + '/u/' + this.state.user.name + search
        break
        case 'default':
          //window.location.href = this.URLbase + search
          this.state.curPage = 0
          this.state.fetchUserLinks(this.state.loggedinUserID)
          history.pushState(null,null,this.URLbase + '/' + (this.state.curPage + 1))
        break
        case 'track':
          window.location.href = this.URLbase + '/track/' + this.state.curTrack + search
        break
      }
    },
    jumpToPage(pageNo){
      let search = this.state.search.string ? ('/' + (this.state.search.string)) : ''
      switch(this.state.mode){
        case 'u':
        window.location.href = this.URLbase + '/u/' + this.user.name + '/' + pageNo + search
        break
        case 'default':
          this.state.curPage = Math.max(0, Math.min(this.state.totalPages-1, pageNo))
          if(this.state.loggedIn) this.state.fetchUserLinks(this.state.loggedinUserID)
          if(this.state.curPage){
            history.pushState(null,null,this.URLbase + '/' + (this.state.curPage + 1))
          }else{
            history.pushState(null,null,this.URLbase)
          }
        break
        case 'track':
        window.location.href = this.URLbase + '/track/' + this.decToAlpha(this.state.curTrack) + '/' + pageNo + search
        break
      }
    },
    lastPage(){
      let search = this.state.search.string ? ('/' + (this.state.search.string)) : ''
      switch(this.state.mode){
        case 'u':
          window.location.href = this.URLbase + '/u/' + this.state.user.name + '/' + this.state.totalUserPages + search
        break
        case 'default':
          //window.location.href = this.URLbase + '/' + this.state.totalPages + search
          this.state.curPage = this.state.totalPages - 1
          if(this.state.loggedIn) this.state.fetchUserLinks(this.state.loggedinUserID)
          console.log('curPage', this.state.curPage)
          history.pushState(null,null,this.URLbase + '/' + (this.state.curPage + 1))
        break
        case 'track':
          window.location.href = this.URLbase + '/track/' + this.decToAlpha(this.state.curTrack) + '/' + this.state.totalPages + search
        break
      }
    },
    advancePage(){
      let search = this.state.search.string ? ('/' + (this.state.search.string)) : ''
      switch(this.state.mode){
        case 'u':
          window.location.href = this.URLbase + '/u/' + this.state.user.name + '/' + (this.state.curUserPage + 2) + search
        break
        case 'default':
          //window.location.href = this.URLbase + '/' + (this.state.curPage + 2) + search
          if(this.state.curPage < this.state.totalPages-1) this.state.curPage++
          if(this.state.loggedIn) this.state.fetchUserLinks(this.state.loggedinUserID)
          history.pushState(null,null,this.URLbase + '/' + (this.state.curPage + 1))
        break
        case 'track':
          window.location.href = this.URLbase + '/track/' + this.decToAlpha(this.state.curTrack) + '/' +(this.state.curPage + 2) + search
        break
      }
    },
    regressPage(){
      let search = this.state.search.string ? ('/' + (this.state.search.string)) : ''
      switch(this.state.mode){
        case 'u':
          window.location.href = this.URLbase + '/u/' + this.state.user.name + '/' + this.state.curUserPage + search
        break
        case 'default':
          //window.location.href = this.URLbase + '/' + this.state.curPage + search
          if(this.state.curPage) this.state.curPage--
          if(this.state.loggedIn) this.state.fetchUserLinks(this.state.loggedinUserID)
          if(this.state.curPage){
            history.pushState(null,null,this.URLbase + '/' + (this.state.curPage + 1))
          }else{
            history.pushState(null,null,this.URLbase)
          }
        break
        case 'track':
          window.location.href = this.URLbase + '/track/' + this.decToAlpha(this.state.curTrack) + '/' +(this.state.curPage + 2) + search
        break
      }
    },
    downloadLink(link, fileName){
      let a = document.createElement('a')
      a.download = fileName
      a.href = link.href
      a.style.position = 'absolute'
      a.style.opacity = .01
      document.body.appendChild(a)
      a.click()
      a.remove()
    },
    openCollection(collection){
      open(`${this.URLbase}/col/${collection.slug}/view` , '_blank')
    },
    openLink(link){
      open(`${this.URLbase}/` + link.href, '_blank')
    },
    copyLink(val){
      let copyEl = document.createElement('div')
      copyEl.innerHTML = this.URLbase + '/' + val
      copyEl.style.opacity = .01
      copyEl.style.position = 'absolute'
      document.body.appendChild(copyEl)
      var range = document.createRange()
      range.selectNode(copyEl)
      window.getSelection().removeAllRanges()
      window.getSelection().addRange(range)
      document.execCommand("copy")
      window.getSelection().removeAllRanges()
      copyEl.remove()
      let el = document.querySelector('#copyConfirmation')
      el.style.display = 'block';
      el.style.opacity = 1
      let reduceOpacity = () => {
        if(+el.style.opacity > 0){
          el.style.opacity -= .02 * 2
          if(+el.style.opacity<.1){
            el.style.opacity = 1
            el.style.display = 'none'
          }else{
            setTimeout(()=>{
              reduceOpacity()
            }, 10)
          }
        }
      }
      setTimeout(()=>{reduceOpacity()}, 250)
    },
    closePrompts(){
      this.state.showLoginPrompt = false
      this.state.userSettingsVisible = false
      this.state.showModal = false
      this.state.showPreview = false
      this.state.showCollections = false
      this.state.showCollectionTemplate = false
      this.state.editCollection = []
    },
    getAdminData(){
      let sendData = {
        userName: this.state.loggedinUserName, passhash: this.state.passhash,
      }
      fetch(`${this.URLbase}/` + 'getAdminData.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
      console.log('getAdminData.php, data; ', data)
        if(data[0]){
          this.state.adminData = JSON.parse(data[1])
          this.state.showAssetPreview = Array(this.state.adminData.slugs.length).fill(false)
          this.state.showAvatarPreview= Array(this.state.adminData.users.length).fill(false)
        }
      })
    },
    closeModal(){
      if(this.state.modalQueue.length){
        this.state.modalContent = this.state.modalQueue.shift()
      }else{
        this.state.showModal = false
        this.state.modalContent = ''
      }
    },
    getAvatar(id){
      //if(typeof this.state.userInfo[id] == 'undefined' || !this.state.userInfo[id].avatar){
        return this.state.loggedIn ? (this.state.loggedInUser.avatar == this.state.defaultAvatar ? `${this.state.URLbase}/` + this.state.loggedInUser.avatar : this.state.loggedInUser.avatar) : `${this.state.URLbase}/` + this.state.defaultAvatar
      //} else {
      //  this.state.userInfo[id].avatar = this.state.userInfo[id].avatar.replace(' ','')
      //  return this.state.userInfo[id].avatar
      //}
    },
    closePreview(){
      this.state.showPreview = false
      this.state.previewLink = null
    },
    checkEnabled(){
      if(this.state.loggedinUserName) {
        let sendData = {
          userName: this.state.loggedinUserName, passhash: this.state.passhash,
        }
        fetch(`${this.URLbase}/` + 'checkEnabled.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(!!(+data[0])){
            console.log('logged in.')
            this.state.loggedIn= true
            this.state.loggedinUserID = +data[1]
            this.state.loggedInUser.avatar = data[2]
            this.state.username = this.state.regusername || this.state.loggedinUserName
            //this.state.fetchUserData(this.state.loggedinUserID)
            this.setCookie()
            this.state.loginPromptVisible = false
            this.state.invalidLoginAttemp = false
            //this.state.userInfo[this.state.loggedinUserID] = {}
            //this.state.userInfo[this.state.loggedinUserID].name = this.state.regusername || this.state.loggedinUserName
            //this.state.userInfo[this.state.loggedinUserID].avatar = data[2]
            //this.state.userInfo[this.state.loggedinUserID].isAdmin = +data[3]
            if(+data[3]) this.state.isAdmin = true
            
            
            //this.fetchUserLinks(this.state.loggedinUserID)
            
            
            //this.state.maxResultsPerPage = +data[4]
          }else{
            console.log('not logged in.')
            this.state.loadingAssets = false
            this.state.loggedIn= false
            this.state.loggedinUserName = ''
            this.state.loggedinUserID = ''
            this.state.passhash = ''
            this.state.isAdmin = false
            this.state.invalidLoginAttempt = true
          }
          this.getMode()
        })
      }
    },
    setLinksOwner(){
      if(!this.state.links.length) return
      let sendData = {
        userName: this.state.username,
        passhash: this.state.passhash,
        ids: JSON.parse(JSON.stringify(this.state.links)).map(v=>{ return v.id})
      }
      fetch(`${this.URLbase}/` + 'setOwner.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      }).then(res => res.json()).then(data=>{
        console.log('res from setOwner.php: ', data)
        if(!data[0]) alert('error setting link owner')
      })
    },
    loadCollection(id, show=false){
      this.state.loadingCollections = true
      let sendData = {
        userID: this.state.loggedinUserID ? -1 : this.state.loggedinUserID,
        passhash: this.state.loggedinUserID ? '' : this.state.passhash,
        //page: this.state.collectionsPage,
        //maxResultsPerPage: this.state.maxCollectionResultsPerPage
        collectionID: +id
      }
      fetch(`${this.URLbase}/` + 'loadCollection.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      }).then(res => res.json()).then(data => {
        this.state.loadingCollections = false
        console.log('data', data)
        if(!!(+data[0])){
          if(!this.state.collections.filter(v=>+v.id==+id).length){
            this.state.collections = [...this.state.collections, data[1]]
          }
          this.state.showCollectionTemplate = false
          this.viewCollection(data[1])
          if(!show){
            this.$nextTick(()=>{
              this.state.showPreview = false
            })
          }
        }
      })
    },
    fetchCollections(userID){
      this.state.loadingCollections = true
      let sendData = {
        userID,
        passhash: this.state.passhash,
        page: this.state.collectionsPage,
        maxResultsPerPage: this.state.maxCollectionResultsPerPage
      }
      fetch(`${this.URLbase}/` + 'fetchCollections.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      }).then(res => res.json()).then(data => {
        this.state.loadingCollections = false
        if(!!(+data[0])){
          this.state.collections = data[1]
          this.state.showCollectionTemplate = false
        }
      })
    },
    fetchUserLinks(userID){
      if(this.state.loggedinUserName) {
        let sendData = {
          userID,
          page: this.state.curPage,
          maxResultsPerPage: this.state.maxResultsPerPage
        }
        fetch(`${this.URLbase}/` + 'fetchUserLinks.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          this.state.loadingAssets = false
          if(!!(+data[0])){
            this.state.userLinks = []
            this.state.links = []
            data[1].map((v, i) => {
              let obj = {
                size: +data[2][i].size,
                type: data[2][i].type,
                selected: false,
                ct: i,
                href: v,
                userID: +data[2][i].userID,
                id: +data[2][i].id,
                slug: data[2][i].slug,
                descriptioin: data[2][i].description,
                originalSlug: data[2][i].originalSlug,
                originalDate: data[2][i].originalDate,
                filetype: data[2][i].filetype,
                origin: data[2][i].origin,
                hash: data[2][i].hash,
                name: data[2][i].name,
                date: data[2][i].date,
                private: !!(+data[2][i].private),
                linkType: 'userLink',
                serverTZO: data[2][i].serverTZO,
                views: data[2][i].views
              }
              this.state.fetchEventTally++
              this.state.userLinks.push(obj)
            })
            this.state.totalPages = +data[3]
            if(this.state.curPage+1 > this.state.totalPages) this.lastPage()
            this.fetchCollections(userID)
          }
        })
      }
    },
    syncCache(){
      this.state.links.map(link=>{
        if(!this.state.cacheLinks.filter(link_=>link_.id == link.id).length){
          this.state.cacheLinks = [...this.state.cacheLinks, link]
        }
      })
      this.state.userLinks.map(link=>{
        if(!this.state.cacheLinks.filter(link_=>link_.id == link.id).length){
          this.state.cacheLinks = [...this.state.cacheLinks, link]
        }
      })
      this.state.miscLinks.map(link=>{
        if(!this.state.cacheLinks.filter(link_=>link_.id == link.id).length){
          this.state.cacheLinks = [...this.state.cacheLinks, link]
        }
      })
    },
    updateCollection(colData){
      let sendData = {
        userID: this.state.loggedinUserID,
        passhash: this.state.passhash,
        colData
      }
      console.log(sendData)
      fetch(`${this.URLbase}/` + 'updateCollection.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      }).then(res => res.json()).then(data => {
        console.log(data)
        if(data[0]){
        }else{
          console.log('there was an error updating the collection')
        }
      })
    },
    selectAll(){
      this.state.links.map(v=>{
        v.selected = true
      })
      this.state.userLinks.map(v=>{
        v.selected = true
      })
    },
    deSelectAll(){
      this.state.links.map(v=>{
        v.selected = false
      })
      this.state.userLinks.map(v=>{
        v.selected = false
      })
    },
    deleteSelected(){
      let count = 0
      let confirmed = false
      let linksToProcess = []
      let userLinksToProcess = []
      let miscLinksToProcess = []
      let slugs = []
      this.state.links.map((v, i) => {
        if(v.selected){
          count++
          linksToProcess = [...linksToProcess, v.id]
          slugs = [...slugs, v.slug]
        }
      })
      this.state.userLinks.map((v, i) => {
        if(v.selected){
          count++
          userLinksToProcess = [...userLinksToProcess, v.id]
          slugs = [...slugs, v.slug]
        }
      })
      this.state.miscLinks.map((v, i) => {
        if(v.selected){
          count++
          miscLinksToProcess = [...miscLinksToProcess, v.id]
          slugs = [...slugs, v.slug]
        }
      })
      if(!count) return confirmed
      let prmpt = prompt(`\n\nARE YOU SURE YOU WANT TO DELETE ${count} ITEM${count>1?'S':''}?\n\n\n   >>> THIS ACTION CANNOT BE UNDONE! <<<\n\n\n  type 'yes' to continue"`)
      if(prmpt && prmpt.toLowerCase().indexOf('yes') != -1){
        confirmed = true
        console.log('deleting selected...')
        let sendData = {
          userName: this.state.username,
          passhash: this.state.passhash,
          slugs
        }
        console.log('sendData', sendData)
        fetch(`${this.URLbase}/` + 'delete.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json()).then(data => {
          console.log(data)
          if(data[0]){
            this.state.links = this.state.links.filter((v, i) => !linksToProcess.filter(q => q == v.id).length)
            this.state.userLinks = this.state.userLinks.filter((v, i) => !userLinksToProcess.filter(q => q == v.id).length)
            this.state.miscLinks = this.state.miscLinks.filter((v, i) => !miscLinksToProcess.filter(q => q == v.id).length)
            this.state.deleteEventTally++
            this.state.jumpToPage(this.state.curPage-1)
            console.log(`deleted ${count} items`)
          }else{
            alert(`there was a problem deleting ${slugs.length > 1 ? 'these' : 'this'} asset${slugs.length > 1 ? 's' : ''}`)
          }
        })
      }
      return confirmed
    },
    createCollection(colData){
      let sendData = {
        userID: this.state.loggedinUserID,
        passhash: this.state.passhash,
        colData
      }
      console.log(sendData)
      fetch(`${this.URLbase}/` + 'createCollection.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      }).then(res => res.json()).then(data => {
        console.log(data)
        if(data[0]){
          this.state.collections = [...this.state.collections, data[1]]
        }else{
          console.log('there was an error creating the collection')
        }
      })
    },
    deleteCollection(collection){
      let prmpt = prompt(`\n\nARE YOU SURE YOU WANT TO DELETE THIS COLLECTION?\n\n\n   it contains ${collection.meta.slugs.length} items\n\n\n>>> THIS ACTION CANNOT BE UNDONE! <<<\n\n\n  type 'yes' to continue"`)
      if(prmpt && prmpt.toLowerCase().indexOf('yes') != -1){
        let sendData = {
          userName: this.state.username,
          passhash: this.state.passhash,
          collectionID: collection.id
        }
        console.log('sendData', sendData)
        fetch(`${this.URLbase}/` + 'deleteCollection.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          console.log(data)
          if(data[0]){
            console.log(`deleted collection id: ${collection.id}`)
            this.state.collections = this.state.collections.filter(v => +v.id !== +collection.id)
          }else{
            alert(`there was a problem!`)
          }
        })
      }
    },
    deleteSingle(link, override=true){
      if(override && this.state.editCollection.length){
        let sendCollObj = escape(JSON.stringify({function: 'delete', name: 'collection', link}))
        let sendAcctObj = escape(JSON.stringify({function: 'delete', name: 'account', link}))
        this.state.modalContent = `<div style="width: 500px; padding: 50px; background: #400b; position:absolute; text-align: center;font-size: 24px; color: white; top: 50%; left: 50%; transform: translate(-50%, -50%);">delete how?<br><br><button style="width: 375px;" onclick="window.choose('${sendCollObj}')">from this collection ONLY</button><br><br><button style="width: 375px; background: #f44;" onclick="window.choose('${sendAcctObj}')">from ACCOUNT AND ALL COLLECTIONS</button></div>`
        this.state.showModal = true
      }else{
        let lsel = []
        let ulsel = []
        this.state.links.map(v=>{
          lsel = [...lsel, v.selected]
        })
        this.state.userLinks.map(v=>{
          ulsel = [...ulsel, v.selected]
        })
        this.deSelectAll()
        this.state.links.map(v=>{
          if(v.id == link.id) v.selected = true
        })
        this.state.userLinks.map(v=>{
          if(v.id == link.id) v.selected = true
        })
        this.deleteSelected()
        lsel.map((v, i) => {
          this.state.links[i].selected = v
        })
        ulsel.map((v, i) => {
          this.state.userLinks[i].selected = v
        })
        this.state.showPreview = false
      }
    },
    multipleLinks(){
      return (this.state.mode == 'col' && this.state.miscLinks.length > 1) ||
             (this.state.userLinks.length > 1 || this.state.links.length > 1) ||
             (this.state.miscLinks > 1 && (this.state.showEditCollection || this.state.showCollection))
    },
    setCookie() {
      let cookies = document.cookie
      cookies.split(';').map(v=>{
        if(v.indexOf('autoplay')==-1){
          document.cookie = v + '; expires=' + (new Date(0)).toUTCString() + '; path=/; domain=' + this.state.rootDomain
        }
      })
      document.cookie = 'loggedinuser=' + this.state.loggedinUserName + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'loggedinuserID=' + this.state.loggedinUserID + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'token=' + this.state.passhash + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'autoplay=' + this.state.autoplay + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'showControls=' + this.state.showControls + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
    },
    login(){
      let sendData = {userName: this.state.username, password: this.state.password}
      fetch(`${this.URLbase}/` + 'login.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          this.state.modalContent = ''
          this.state.showModal = false
          console.log('login succeeded!')
          this.state.loggedIn= true
          this.state.loggedinUserName = this.state.username
          this.state.loggedinUserID = +data[2]
          //this.state.fetchUserData(this.state.loggedinUserID)
          this.state.isAdmin = +data[4]
          this.state.passhash = data[1]
          this.setCookie()
          this.closePrompts()
          this.state.invalidLoginAttemp = false
          this.state.loggedInUser.avatar = data[3]
          this.setLinksOwner()
          this.fetchUserLinks(this.state.loggedinUserID)
          this.state.links = []
        }else{
          console.log('not logged in.')
          this.state.loggedIn= false
          this.state.invalidLoginAttempt = true
        }
      })
    },
    isNumber(val){
      return val>-1e50&&+val<1e50
    },
    getMode(){
      let vars = window.location.pathname.split('/').filter(v=>v && ''+v != 'NaN')
      if(vars.length>0){
        console.log('vars', vars)
        let l = location.origin.toLowerCase().indexOf('000webhostapp.com') !== -1 ? 1 : 0
        if(this.state.isNumber(vars[l])){
          this.state.mode = 'default'
          let search = ''
          
          if(vars[l]){
            this.state.curPage = (+vars[l])-1
            if(''+this.state.curPage == 'NaN') this.state.curPage = 0
            if(vars[l+1]){
              this.state.search.string = decodeURIComponent(vars[l+1])
              search = '/' + vars[l+1]
              //history.pushState(null,null,this.URLbase + '/' + (this.state.curPage + 1)) + search
              //this.beginSearch()
              this.state.curPage = 0
              this.state.jumpToPage(0)
              console.log('flow ',1)
              //if(location.href !== this.URLbase + '/1') location.href = this.URLbase + '/1'
            }else{
              history.pushState(null,null,this.URLbase + '/' + this.state.curPage ? (this.state.curPage + 1) : '')
              if(!this.state.curPage || this.state.curPage < 0 || this.state.curPage > 1e6) this.state.curPage = 0
              this.fetchUserLinks(this.state.loggedinUserID)
              console.log('flow ',2)
            }
          }else{
            if(location.href !== this.URLbase + '/1') history.pushState(null,null,this.URLbase + '/1')
            this.state.mode = 'default'
          }
        }else{
          this.state.mode = vars[l].trim()
          console.log('non-default mode detected: ', this.state.mode)
          switch(this.state.mode){
            case 'col':
              if(typeof vars[l+1] != 'undefined'){
                let show = false
                if(typeof vars[l+2] != 'undefined'){
                  switch(vars[l+2]){
                    case 'view':
                      show = true
                    break
                  }
                }
                this.loadCollection(this.alphaToDec(vars[l+1]), show)
              } else {
                if(location.href !== this.URLbase + '/1') history.pushState(null,null,this.URLbase + '/1')
                this.state.mode = 'default'
                this.state.curPage = 0
                this.fetchUserLinks(this.state.loggedinUserID)
              }
            break
          }
        }
      } else{
        if(location.href !== this.URLbase + '/1') history.pushState(null,null,this.URLbase + '/1')
        console.log('flow ',3)
        this.state.curPage = 0
        if(this.state.loggedIn) this.fetchUserLinks(this.state.loggedinUserID)
        this.state.mode = 'default'
      }
      console.log('mode', this.state.mode)
    },
    setLinkPropertySelected(property, value){
      this.state.links.map(link=>{
        if(link.selected) this.setLinkProperty(link, property, value)
      })
      this.state.userLinks.map(link=>{
        if(link.selected) this.setLinkProperty(link, property, value)
      })
      this.state.miscLinks.map(link=>{
        if(link.selected) this.setLinkProperty(link, property, value)
      })
      this.state.cacheLinks.map(link=>{
        if(link.selected) this.setLinkProperty(link, property, value)
      })
    },
    setLinkProperty(link, property, value, override=true, force=false){
      if(override && this.state.editCollection.length){
        let sendObj = escape(JSON.stringify({
          function: 'visibility',
          link,
          property,
          value
        }))
        this.state.modalContent = `<div style="font-size: .85em; text-align: left; width: 500px; padding: 50px; background: #400b; position:absolute; color: white; top: 50%; left: 50%; transform: translate(-50%, -50%);">Changes to the visibility of single assets are system-wide, which may affect public viewability of this item in other collections.<br><br> You will still see all your own items if logged in, regardless.<br><br>To make an item private in one collection but not another, create a separate, private collection for such items.<br><br><button style="width: 375px;" onclick="window.choose('${sendObj}')">OK, got it!</button></div>`
        this.state.showModal = true
      }else{
        console.log('continuing with setLinkProperty', link, property, value)
        if(link[property] != value || force){
          link[property] = value
          let sendData = {
            userName: this.state.loggedinUserName,
            passhash: this.state.passhash,
            linkID: link.id,
            property,
            value,
          }
          fetch(`${this.URLbase}/` + 'setLinkProperty.php',{
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data=>{
            console.log('setLinkProperty data: ', data)
            if(data[0]){
              if(!force){
                this.state.links.map(v => { if(v.slug === link.slug) v[property] = value })
                this.state.userLinks.map(v => { if(v.slug === link.slug) v[property] = value })
                this.state.miscLinks.map(v => { if(v.slug === link.slug) v[property] = value })
                this.state.cacheLinks.map(v => { if(v.slug === link.slug) v[property] = value })
              }
            }else{
              alert('there was a problem setting the property! d\'oh!')
            }
          })
        }
      }
    },
    loadLinks(slugs, forCollection=false){
    
      slugs = slugs.filter(v=>v)
      let cullSlgs = []
      let tgtSlugs = JSON.parse(JSON.stringify(slugs))
      
      this.state.miscLinks = this.state.miscLinks.filter(link => {
        let keep = !!tgtSlugs.filter(tgtSlg=>tgtSlg==link.slug).length
        if(keep) cullSlgs = [...cullSlgs, link.slug]
        return keep
      })
      tgtSlugs = tgtSlugs.filter(slug => !cullSlgs.filter(slug_=> slug_==slug).length)
      tgtSlugs.map(tgtSlg => {
        this.state.links.map(link => {
          if(link.slug == tgtSlg){
            cullSlgs = [...cullSlgs, tgtSlg]
            this.state.miscLinks=[...this.state.miscLinks, link]
          }
        })
      })
      tgtSlugs = tgtSlugs.filter(slug => !cullSlgs.filter(slug_=> slug_==slug).length)
      tgtSlugs.map(tgtSlg => {
        this.state.userLinks.map(link => {
          if(link.slug == tgtSlg){
            cullSlgs = [...cullSlgs, tgtSlg]
            this.state.miscLinks=[...this.state.miscLinks, link]
          }
        })
      })
      tgtSlugs = tgtSlugs.filter(slug => !cullSlgs.filter(slug_=> slug_==slug).length)

      if(tgtSlugs.length){
        let sendData = { slugs: tgtSlugs }
        fetch(`${this.URLbase}/` + 'loadLinks.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data=>{
          console.log(data)
          if(data[0]){
            data[1].map((v, i) => {
              let obj = {
                size: +data[2][i].size,
                type: data[2][i].type,
                selected: false,
                ct: i,
                href: v,
                userID: +data[2][i].userID,
                id: +data[2][i].id,
                name: data[2][i].name,
                description: data[2][i].description,
                filetype: data[2][i].filetype,
                slug: data[2][i].slug,
                hash: data[2][i].hash,
                originalSlug: data[2][i].originalSlug,
                originalDate: data[2][i].originalDate,
                origin: data[2][i].origin,
                date: data[2][i].date,
                private: !!(+data[2][i].private),
                linkType: 'userLink',
                serverTZO: data[2][i].serverTZO,
                views: data[2][i].views
              }
              this.state.miscLinks=[...this.state.miscLinks, obj]
            })
            if(!this.state.miscLinks.length) location.href = location.origin
            if(forCollection) {
              this.state.previewLink = this.state.miscLinks[0]
              this.state.showPreview = true
            } else {
              this.state.showPreview = false
            }
          }else{
            console.log('there was a problem loading the link', data)
          }
        })
      }
    },
    showEditCollection(collection){
      console.log('collection', collection)
      this.state.editCollection = [collection]
    },
    setCollectionProperty(collection, property, value){
      if(collection.meta[property] != value){
        collection.meta[property] = value
        let obj = {
          name: collection.name,
          id: collection.id,
          description: collection.meta.description,
          slugs: collection.meta.slugs,
          private: collection.meta.private,
        }
        this.updateCollection(obj)
      }
    },
    logout(){
      history.pushState(null,null,this.URLbase)
      let cookies = document.cookie
      cookies.split(';').map(v=>{
        if(v.indexOf('autoplay')==-1){
          document.cookie = v + '; expires=' + (new Date(0)).toUTCString() + '; path=/; domain=' + this.state.rootDomain
        }
      })
      //if(this.state.search.string != '') this.state.search.demos = this.state.search.demos.filter(v=>!v.private)
      switch(this.state.mode){
        case 'user':
        //this.state.user.demos = this.state.user.demos.filter(v=>!v.private)
        break
        case 'single':
        //this.state.demos = this.state.demos.filter(v=>!v.private)
        break
        case 'default':
        //this.state.landingPage.demos = this.state.landingPage.demos.filter(v=>!v.private)
        break
      }
      this.state.loggedIn= false
      this.state.isAdmin = false
      this.state.loggedinUserID = this.state.loggedinUserName = ''
      window.location.reload()
    },
    views(link){
      return 'views: ' + link.views.toLocaleString()
    },
    size(size){
      let MB_ = 1024**2
      let tbytes = size
      let MB = tbytes / MB_ | 0
      let KB = ((tbytes / MB_) - MB) * MB_ / 1024 | 0
      let B = (((tbytes / MB_) - MB) * MB_ / 1024 - KB) * KB | 0
      let ret
      if(MB){
        ret = (Math.round(tbytes / MB_*100)/100) + ' MB'
      } else if(KB) {
        ret = (Math.round(((tbytes / MB_) - MB) * MB_ / 1024*100)/100) + ' KB'
      } else {
        ret = size.toLocaleString() + ' B'
      }
      return ret
    },
    addLink(size, type, ct, href, selected, userID, slug, originalSlug, origin, serverTZO, views, id, date, originalDate, visibility, name){
      let obj = {
        size,
        type,
        name,
        ct,
        href,
        slug,
        origin,
        selected,
        userID,
        id,
        originalSlug,
        linkType: 'link',
        serverTZO,
        views,
        date,
        originalDate,
        visibility
      }
      this.state.links.push(obj)
    },
    age(link){
      let tseconds = (((new Date()) - (new Date(link.date)))/1000|0) + 3600 * (((new Date).getTimezoneOffset()/60) - (location.origin.toLowerCase().indexOf('000webhostapp') === -1 ? 4: 0))
      let years = (tseconds/31536000)|0
      let days = (((tseconds/31536000)-years) * 31536000) / 86400 | 0
      let hours = (((((tseconds/31536000)-years) * 31536000) / 86400) - days) * 86400 / 3600 | 0
      let minutes = (((((((tseconds/31536000)-years) * 31536000) / 86400) - days) * 86400 / 3600) - hours) * 3600 / 60 | 0
      let seconds = (((((((((tseconds/31536000)-years) * 31536000) / 86400) - days) * 86400 / 3600) - hours) * 3600 / 60) - minutes) * 60| 0
      let ret = ''
      ret += years ? `${years} year${years>1?'s':''}, ` : ''
      ret += days ? `${days} day${days>1?'s':''}, ` : ''
      ret += hours ? `${hours} hour${hours>1?'s':''}, ` : ''
      ret += minutes ? `${minutes} minute${minutes>1?'s':''}` : ''
      //ret += seconds? `${seconds} second${seconds>1?'s':''}` : ''
      return ret ? ret : 'added just now...'
    },
    prettyDate(link){
      return (new Date(link.date)).toLocaleString().split(',')[0]
    },
    firstSeen(link){
      let tseconds = (((new Date()) - (new Date(link.originalDate)))/1000|0) + 3600 * (((new Date).getTimezoneOffset()/60) - 4)
      let years = (tseconds/31536000)|0
      let days = (((tseconds/31536000)-years) * 31536000) / 86400 | 0
      let hours = (((((tseconds/31536000)-years) * 31536000) / 86400) - days) * 86400 / 3600 | 0
      let minutes = (((((((tseconds/31536000)-years) * 31536000) / 86400) - days) * 86400 / 3600) - hours) * 3600 / 60 | 0
      let seconds = (((((((((tseconds/31536000)-years) * 31536000) / 86400) - days) * 86400 / 3600) - hours) * 3600 / 60) - minutes) * 60| 0
      let ret = ''
      ret += years ? `${years} year${years>1?'s':''}, ` : ''
      ret += days ? `${days} day${days>1?'s':''}, ` : ''
      ret += hours ? `${hours} hour${hours>1?'s':''}, ` : ''
      ret += minutes ? `${minutes} minute${minutes>1?'s':''}` : ''
      //ret += seconds? `${seconds} second${seconds>1?'s':''}` : ''
      return ret ? ret : 'just now...'
    },
    fileName(link){
      let ret = link.origin.split(': ')[1]
      if(ret.length > 23) ret = ret.substring(0, 10) + '...' + ret.substring(ret.length-10)
      return ret
    },    
    shortText(text, maxlen){
      if(text){
        if(text.length > maxlen) text = text.substring(0, (maxlen-3)/2|0) + '...' + text.substring(text.length-(maxlen-3)/2|0)
        return text
      }
    },    
    fullFileName(link){
      let l
      let originalSuffix = (l = link.origin.split(': ')[1].split('.'))[l.length-1]
      if(link.name.indexOf(`.${originalSuffix}`) === -1) return link.name + `.${originalSuffix}`
      return link.name
    },    
    checkLogin(){
      let l = (document.cookie).split(';').filter(v=>v.split('=')[0].trim()==='loggedinuser')
      if(l.length){
        this.state.loggedinUserName = l[0].split('=')[1]
        this.state.username = this.state.loggedinUserName
        let l2 = (document.cookie).split(';').filter(v=>v.split('=')[0].trim()==='token')
        if(l2.length){
          this.state.passhash = l2[0].split('=')[1]
          let l3 = (document.cookie).split(';').filter(v=>v.split('=')[0].trim()==='loggedinuserID')
          if(l3.length){
            this.state.loggedinUserID = +l3[0].split('=')[1]
            this.checkEnabled()
          }
        }
      } else {
        this.state.loadingAssets = false
        this.getMode() 
      }
      //this.checkShowControlsPref()
      //this.checkAutoplayPref()
      //this.checkExactSearchPref()
    }
  },
  watch: {
    'state.choice'(val){
      val = JSON.parse(unescape(val))
      switch(val.function){
        case 'delete':
          switch(val.name){
            case 'collection':  // delete asset from
              this.state.modalContent = ''
              this.state.showModal = false
              this.state.collections = this.state.collections.map(collection => {
                collection.meta.slugs = collection.meta.slugs.filter(slug => {
                  return slug !== val.link.slug
                })
                let obj = {
                  name: collection.name,
                  id: collection.id,
                  description: collection.meta.description,
                  slugs: collection.meta.slugs,
                  private: collection.meta.private,
                }
                this.updateCollection(obj)
                return collection
              })
              this.state.deleteEventTally++
              this.state.miscLinks = this.state.miscLinks.filter((v, i) => {
                return v.slug != val.link.slug
              })
            break
            case 'account':  // delete asset from
              this.state.modalContent = ''
              this.state.showModal = false
              this.state.collections = this.state.collections.map(collection => {
                collection.meta.slugs = collection.meta.slugs.filter(slug => {
                  return slug !== val.link.slug
                })
                let obj = {
                  name: collection.name,
                  id: collection.id,
                  description: collection.meta.description,
                  slugs: collection.meta.slugs,
                  private: collection.meta.private,
                }
                this.updateCollection(obj)
                return collection
              })
              this.deleteSingle(val.link, false)
            break
          }
        break
        case 'visibility':
          this.setLinkProperty(val.link, val.property, val.value, false)
          this.state.modalContent = ''
          this.state.showModal = false
        break
      }
    },
    linksChange(val){
      //console.log(`detected linksChange event: val:${val}`)
      this.syncCache()
      console.log(`cache synced, new cache size: ${this.state.cacheLinks.length} items`, this.state.cacheLinks)
      
    },
    'state.loadingAssets' (val){
      if(val){
        this.state.modalContent = '<div style="width: 500px; height: 100px; position:absolute; text-align: center;font-size: 24px; color: white; top: 50%; left: 50%; transform: translate(-50%, -50%);">... loading ...</div>'
        this.state.showModal = true
      }else{
        this.state.modalContent = ''
        this.state.showModal = false
      }
    },
    'state.uploadInProgress' (val) {
      /*console.log('state.uploadInProgress val', val)
      if(val){
        this.state.modalContent = `
          loading...
        `
        this.state.showModal = true
      }else{
        this.state.modalContent = ''
        this.state.showModal = false
      }
      */
    }
  },
  computed:{
    linksChange(){
      return this.state.uploadEventTally * 1e1 + 
             this.state.fetchEventTally  * 1e2 + 
             this.state.deleteEventTally * 1e3 +
             this.state.links.length*1e2 * 1e4 +
             this.state.userLinks.length * 1e5 +
             this.state.miscLinks.length * 1e6
    },
    showAdminButton(){
      return this.state.loggedIn && 
             this.state.isAdmin && 
             !this.state.showModal &&
             !this.state.showPreview &&
             !this.state.showLogin &&
             !this.state.showCollections
    },
    URLbase(){
      let ret = window.location.origin
      if(ret.toLowerCase().split('.')[0].indexOf('imjur') === -1 &&
         ret.toLowerCase().split('.')[0].indexOf('assets') === -1 ){
        ret += '/imjur'
      }
      return ret
    },
    popupVisible(){
      return this.state.userSettingsVisible ||
      this.state.showLoginPrompt ||
      this.state.showCollections ||
      this.state.editCollection.length ||
      this.state.showCollectionTemplate ||
      this.state.showPreview ||
      this.state.showUploadModal ||
      this.state.showRegister
    }
  },
  mounted(){
    document.body.style.backgroundImage = `url(./assets/new_bg.jpg)`
    window.choose = choice => {
      this.state.choice = choice
    }
    this.state.doMouseDown = window.onmousedown = e => {
      this.state.keys[18] = false
      this.state.click = true
      this.$nextTick(() => {
        this.state.click = false
      })
    }
  
    window.onkeyup = e =>{
      e.preventDefault()
      e.stopPropagation()
      this.state.keys[e.keyCode] = false
      this.state.keys[18] = false
      this.state.click = true
      this.$nextTick(() => {
        this.state.click = false
      })
    }
    
    this.state.onkeydown = window.onkeydown = e => {
      this.state.keys[e.keyCode] = true
      console.log(e.keyCode)
      if(this.state.keys[18]){
        e.preventDefault()
        e.stopPropagation()
      }
      switch(e.keyCode){
        case 116:
          window.location.reload()
        break
        case 65:
          if(this.state.keys[17]){
            e.preventDefault()
            e.stopPropagation()
            if(this.state.keys[16]){ // ctrl + shift + a
              this.deSelectAll()
            }
            if(!this.state.keys[16]){ // ctrl + a
              this.selectAll()
            }
          }
        break
        case 46:
          this.deleteSelected()
        break
        case 37: // left arrow
          if(this.state.showPreview){
            this.prev()
          }else{
            if(this.state.keys[18] && this.state.curPage) this.regressPage()
          }
        break
        case 39: // right arrow
           if(this.state.showPreview){
            this.next()
          }else{
            if(this.state.keys[18] && this.state.curPage < this.state.totalPages) this.advancePage()
          }
        break
        case 36: // home
          if(this.state.keys[18]) this.firstPage()
        break
        case 35: // end
          if(this.state.keys[18]) this.lastPage()
        break
        case 27:
          this.state.showPreview = false
          this.showLoading = false
          if(!this.state.uploadInProgress && !this.state.showLoginPrompt) this.state.showModal = false
          this.closePrompts()
        break
      }
    }
    this.state.age = this.age
    this.state.size = this.size
    this.state.prev = this.prev
    this.state.next = this.next
    this.state.views = this.views
    this.state.login = this.login
    this.state.logout = this.logout
    this.state.addLink = this.addLink
    this.state.URLbase = this.URLbase
    this.state.fileName = this.fileName
    this.state.copyLink = this.copyLink
    this.state.openLink = this.openLink
    this.state.register = this.register
    this.state.lastPage = this.lastPage
    this.state.isNumber = this.isNumber
    this.state.getPages = this.getPages
    this.state.loadLinks = this.loadLinks
    this.state.firstSeen = this.firstSeen
    this.state.firstPage = this.firstPage
    this.state.getAvatar = this.getAvatar
    this.state.selectAll = this.selectAll
    this.state.shortText = this.shortText
    this.state.setCookie = this.setCookie
    this.state.syncCache = this.syncCache
    this.state.jumpToPage = this.jumpToPage
    this.state.checkLogin = this.checkLogin
    this.state.closeModal = this.closeModal
    this.state.prettyDate = this.prettyDate
    this.state.advancePage = this.advancePage
    this.state.regressPage = this.regressPage
    this.state.deSelectAll = this.deSelectAll
    this.state.deleteSingle = this.deleteSingle
    this.state.getAdminData = this.getAdminData
    this.state.closePrompts = this.closePrompts
    this.state.fullFileName = this.fullFileName
    this.state.downloadLink = this.downloadLink
    this.state.closePreview = this.closePreview
    this.state.multipleLinks = this.multipleLinks
    this.state.setLinksOwner = this.setLinksOwner
    this.state.fetchUserLinks = this.fetchUserLinks
    this.state.viewCollection = this.viewCollection
    this.state.deleteSelected = this.deleteSelected
    this.state.loadCollection = this.loadCollection
    this.state.openCollection = this.openCollection
    this.state.setLinkProperty = this.setLinkProperty
    this.state.showUserSettings = this.showUserSettings
    this.state.fetchCollections = this.fetchCollections
    this.state.updateCollection = this.updateCollection
    this.state.deleteCollection = this.deleteCollection
    this.state.createCollection = this.createCollection
    this.state.showEditCollection = this.showEditCollection
    this.state.setCollectionProperty = this.setCollectionProperty
    this.state.setLinkPropertySelected = this.setLinkPropertySelected
    this.checkLogin()
  }
}
</script>

<style>
/* latin-ext */
@font-face {
  font-family: 'Courier Prime';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELaw9pWt_-.woff2) format('woff2');
  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Courier Prime';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELawFpWg.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
*{
  box-sizing: border-box;
}
body,html{
  background-color: #000;
  background-repeat: repeat;
  background-size: cover;
  background-position: center center;
  margin: 0;
  height: 100vh;
  min-width: 600px;
  overflow: hidden;
  font-family: Courier Prime;
}
a:visited{
  color: #40f;
}
button:focus{
  outline: none;
}
.modalInner{
  text-align: center;
  padding: 25px;
  width: 100%;
  height: 100%;
  font-size: 25px;
  box-sizing: border-box;
  word-break: break-all;
  color: #fff;
  text-shadow: 2px 2px 2px #000;
  background: #001b;
  word-break: auto-phrase;
}
button{
  font-size: 18px;
  border: 2px solid #0008;
  border-radius: 6px;
  background: #ff0;
  padding: 4px;
  padding-left: 10px;
  padding-right: 10px;
  font-weight: 900;
  min-width: 116px;
  cursor: pointer;
  font-family: Courier Prime;
  color: #000;
  text-shadow: 1px 1px 3px #40f;
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
a{
  text-decoration: none;
  color: #08f;
}
.cancelButton{
  background: #822;
  color: #f88;
  text-shadow: 1px 1px 3px #40f;
  font-weight: 900;
  width: 125px;
  font-family: Courier Prime;
  font-size: 14px;
  border: none;
  border-radius: 10px;
  padding: 5px;
  position: absolute;
  z-index: 1100;
  right: 35px;
  top: 14px;
  min-width: 120px;
}
.tdLeft{
  text-align: right;
  color: #f80;
  border-bottom: 1px solid #4fc2;
  padding: 3px;
  max-width: 65px;
}
.tdRight{
  text-align: left;
  color: #0f8;
  border-bottom: 1px solid #4fc2;
  padding: 3px;
}
.copyLinkButton, .openButton, .downloadButton, .deleteSingleButton, .visibilityButton{
  display: inline-block;
  background-position: center center;
  background-repeat: no-repeat;
  background-image: url(./assets/link.png);
  width: 32px;
  height: 32px;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  margin: 4px;
}
.openButton{
  background-image: url(./assets/open.png);
  background-color: #08f;
  background-size: 80% 80%;
}
.copyLinkButton{
  background-size: contain;
  background-image: url(./assets/link.png);
  background-color: #f06;
}
.downloadButton, .deleteSingleButton, .notPrivate, .private, .notPrivateDisabled, .privateDisabled{
  background-color: #0000;
}
.deleteSingleButton, .notPrivate, .private, .notPrivateDisabled, .privateDisabled{
  background-color: #0000;
  background-size: 100% 100%;
}
.downloadButton{
  background-size: contain;
  background-image: url(./assets/download.png);
  background-size: 52px 37px;
}
.deleteSingleButton{
  background-image: url(./assets/trash.png);
}
.notPrivate{
  background-image: url(./assets/visible.png);
}
.private{
  background-image: url(./assets/nonvisible.png);
}
.notPrivateDisabled{
  background-image: url(./assets/visible_disabled.png);
}
.privateDisabled{
  background-image: url(./assets/nonvisible_disabled.png);
}
.linkButtons{
  margin-top: 0px;
  display: inline-block;
  width: 90px;
  text-align: center;
  line-height: 0;
}
.copyButton{
  display: inline-block;
  width: 30px;
  height: 30px;
  background-image: url(./assets/link.png);
  cursor: pointer;
  z-index: 500;
  background-size: 90% 90%;
  left: calc(50% + 180px);
  background-position: center center;
  background-repeat: no-repeat;
  border: none;
  background-color: #8fcc;
  margin: 5px;
  border-radius: 5px;
  vertical-align: middle;
}
.viewCollectionButton{
  background: #84fd;
}
.viewCollectionButton, .editCollectionButton{
  height: 24px;
  line-height: 14px;
  min-width: 85px;
  padding: 5px;
  margin: unset;
  margin-right: 5px;
}
.collectionsButton{
  line-height: 13px;
  font-weight: 400;
  font-size: 20px;
  height: 24px;
  margin: 0;
  margin-right: 10px;
  min-width: unset;
}
#copyConfirmation{
  display: none;
  position: absolute;
  width: 100vw;
  height: 100vh;
  top: 0;
  left: 0;
  background: #012d;
  color: #8ff;
  opacity: 1;
  text-shadow: 0 0 5px #fff;
  font-size: 46px;
  text-align: center;
  z-index: 10000;
}
#innerCopied{
  position: absolute;
  top: 50%;
  width: 100%;
  z-index: 1020;
  text-align: center;
  transform: translate(0, -50%) scale(2.0, 1);
}
.resultLink{
  text-decoration: none;
  color: #fff;
  background: #4f86;
  padding: 10px;
  display: inline-block;
}
#resultDiv{
  position: absolute;
  margin-top: 50px;
  left: 50%;
  transform: translate(-50%);
}
.input{
  text-align: center;
  font-size: 24px;
  background: #0004;
  border: none;
  border-bottom: 2px solid #2fa4;
  width: 300px;
  color: #ffa;
}
input[type=text], input[type=password]{
  font-family: Courier Prime;
  font-size: 24px;
  background: #0004;
  border: none;
  /*border-bottom: 6px solid #40fa*/;
  width: 300px;
  color: #ffa;
}
input[type=checkbox]{
  transform: scale(1.5);
  margin: 8px;
  margin-left: 5px;
}
input:focus{
  outline: none;
}
button:focus{
  outline: none;
}
select:focus{
  outline: none;
}
/* Customize the label (the checkboxLabel) */
.checkboxLabel {
  padding-left: 35px;
  margin-bottom: -2px;
  cursor: pointer;
  margin: 0;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  text-align: left;
  position: absolute;
  margin-left: 30px;
  z-index: 10;
  max-height: 30px;
}

/* Hide the browser's default checkbox */
.checkboxLabel input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  cursor: pointer;
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  border: 1px solid #2468;
  background-color: #000;
  border-radius: 5px;
}

/* On mouse-over, add a grey background color */
.checkboxLabel:hover input ~ .checkmark {
  background-color: #234;
}

/* When the checkbox is checked, add a blue background */
.checkboxLabel input:checked ~ .checkmark {
  background-color: #208;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.checkboxLabel input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.checkboxLabel .checkmark:after {
  left: 7px;
  top: 1px;
  width: 7px;
  height: 16px;
  border: solid white;
  border-width: 0 5px 5px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
::-webkit-scrollbar {
  width: 20px;
}

::-webkit-scrollbar-track {
  background: #204; 
}
 
::-webkit-scrollbar-thumb {
  background: #308;
  border-radius: 20px;
}

::-webkit-scrollbar-thumb:hover {
  background: #60f; 
}
</style>