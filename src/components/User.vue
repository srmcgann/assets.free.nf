<template>
  <div class="user" :class="{'loggedInWidth':state.loggedIn, 'notLoggedInWidth': !state.loggedIn}">
    <div class="username" v-if="state.loggedIn">
      <span style="font-size: .75em;">welcome,&nbsp;&nbsp;&nbsp;</span><br>
      <button
        class="commentButton"
        @click="state.openUserPage(state.loggedinUserID)"
        v-html="state.shortText(state.userInfo[state.loggedinUserID]?.name, 16)"
        :title="`view ${state.shortText(state.userInfo[state.loggedinUserID]?.name, 16)}'s assets`"
      ></button>
    </div>
    <div v-if="!state.loggedIn" style="display: inline-block;">
      <button
        @click="login()"
        class="loginButton"
        title="sign in"
        style="font-size:16px; line-height:18px;"
      >
        login
      </button>
      <button
        @click="state.register()"
        class="loginButton"
        style="top: 26px"
        title="register anonymously.<br><br>we only need a user name as<br>something to attach your files to"
      >
        register
      </button>
    </div>
    <div v-else style="display: inline-block;">
      <div class="userButtonContainer">
        <button @click="logout()"
          title="log out"
          class="userbutton"
        >logout</button>
        <button @click="userFiles()"
          title="go to your assets"
          style="background-color: #80fc; color: #fff"
          class="userbutton"
        >my assets</button>
        <button @click="showSettings()"
          title="show user settings"
          class="userbutton"
        >settings</button>
        <button @click="explore()"
          title="learn more about similar tools offered"
          style="background-color: #08fc; color: #fff"
          class="userbutton"
        >explore</button>
      </div>
      <div class="loggedIn" style="display: inline-block;">
        <div
          @click="showSettings()"
          class="avatar"
          :title="`logged in as ${state.loggedinUserName}. click to show user settings`"
          :style="`background-image: url(${state.getAvatar()})`"
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  props: [ 'state' ],
  data(){
    return {
    }
  },
  methods: {
    explore(){
      open('https://whr.rf.gd/a/about','_blank')
    },
    userFiles(){
      this.state.fetchUserLinks(this.state.loggedinUserID)
    },
    showSettings(){
      this.state.showUserSettings()
    },
    login(){
      this.state.showRegister = false
      this.state.showLoginPrompt = true
    },
    logout(){
      this.state.logout()
    },
    toggleLogin(){
      if(!this.state.loggedin) this.state.showLoginPrompt()
    }
  },
  mounted(){
  }
}
</script>

<style scoped>
  .user{
    height: 52px;
    color: #4fa;
    font-size: 20px;
    top: 0;
    right: 0;
    position: fixed;
    text-align: center;
    display: inline-block;
  }
  .loggedInWidth{
    min-width: 350px;
  }
  .notLoggedInWidth{
    min-width: 105px;
  }
  .username{
    display: inline-block;
    text-align: right;
    right: 5px;
    line-height: .8em;
    margin-right: 10px;
    vertical-align: top;
    margin-top: 6px;
  }
  .loginButton{
    border-radius: 5px;
    /*background: #4f8;*/
    color: #022;
    text-shadow: 1px 1px 2px #000;
    font-size: 16px;
    min-width: 100px;
    position: absolute;
    left: 0px;
    top: 1px;
    border: 2px solid #0005;
    padding-top: 0;
    padding-bottom: 2px;
  }
  .userButtonContainer{
    float: right;
    margin-left: 5px;
    margin-top: -2px;
    width: 216px;
  }
  .userbutton:focus{
    outline: none;
  }
  .userbutton{
    font-size: 16px;
    margin: 3px;
    margin-bottom: -3px;
    margin-right: 0;
    padding: 1px;
    padding-bottom: 2px;
    margin-left: 1px;
    margin-right: 1px;
    min-width: 105px;
  }
</style>
