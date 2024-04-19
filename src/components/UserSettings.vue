<template>
  <div class="UserSettings">
    <div class="mainContainer">
      <span style="font-size: 28px;line-height: 1em;font-weight: 900;">{{state.loggedinUserName}}
        <br><span style="color: #aff;font-size: 36px;font-weight: 400">settings</span>
      </span>
      <div class="spacerDiv"></div>
      your avatar<br>
      <div class="editAvatarImgContainer">
        <img :src="state.getAvatar()" class="editAvatarImg">
      </div>
      <input style="opacity: 0; position: absolute;z-index: -1;" ref="tabAnchor"
        v-on:keydown.shift.tab="$refs.cancelButton.focus()"
      >
      <input
        type="text"
        ref="avatarURL"
        v-model="state.loggedInUser.avatar"
        class="input avatarInput"
        @input="updateAvatar()"
        @click="$event.target.select()"
        placeholder="URL to an online image..."
      ><br>
      <div v-if="avatarUpdateSuccessful==1" style="color: #4f8; font-size: 18px; background:#055; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; avatar update successful
      </div>
      <div v-if="avatarUpdateSuccessful==-1" style="color: #f44; font-size: 18px; background:#500; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; avatar update NOT successful
      </div>
      <div class="spacerDiv" style="margin-top: 20px"></div>
      change your password<br>
      <input
        type="password"
        ref="currentpassword"
        class="input passwordFields"
        v-model="currentPassword"
        v-on:keydown.enter="savePassword()"
        @click="$event.target.select()"
        @input="showInvalid=false"
        placeholder="current password"
      ><br>
      <input
        type="password"
        ref="newpassword"
        class="input passwordFields"
        :class="{'passwordsDoNotMatch': !passwordsMatch && newPassword && confirmNewPassword, 'passwordsMatch': passwordsMatch && newPassword && confirmNewPassword}"
        v-model="newPassword"
        v-on:keydown.enter="savePassword()"
        @input="showInvalid=false"
        @click="$event.target.select()"
        placeholder="new password"
      ><br>
      <input
        type="password"
        ref="confirmnewpassword"
        v-model="confirmNewPassword"
        @input="showInvalid=false"
        class="input passwordFields"
        :class="{'passwordsDoNotMatch': !passwordsMatch && newPassword && confirmNewPassword, 'passwordsMatch': passwordsMatch && newPassword && confirmNewPassword}"
        @click="$event.target.select()"
        v-on:keydown.enter="savePassword()"
        placeholder="confirm new password"
      ><br>
      <div v-if="savedPassword" style="color: #4f8; font-size: 24px; background:#055; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; password saved
      </div>
      <div v-if="showInvalid" style="color: #f44; font-size: 20px; background:#400;padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; ERROR SAVING PASSWORD!
      </div>
      <div v-if="!savedPassword && !showInvalid && !passwordsMatch && newPassword && confirmNewPassword" style="color: #f00; font-size: 20px">
        &nbsp;&nbsp;&nbsp; passwords do not match
      </div>
      <div v-if="!savedPassword && !showInvalid && passwordsMatch && newPassword && confirmNewPassword" style="color: #0f4">
        &nbsp;&nbsp;&nbsp; <i>passwords match!</i>
      </div>
      <button
        @click="savePassword()"
        ref="cancelButton"
        :disabled="!validate"
        :class="{'disabledButton': !validate}"
        class="userSettingsButton"
      >save</button>      
      <div class="spacerDiv"></div>
      <button @click="closePrompt()"
        v-on:keydown.tab="$refs.tabAnchor.focus()"
        v-on:keydown.shift.tab="$refs.cancelButton.focus()"
        ref="cancelButton"
        style="background: #faa"
        class="userSettingsButton"
      >close / cancel</button>
      <input style="opacity: 0; position: absolute;z-index: -1" ref="endTabAnchor"
        v-on:keydown.shift.tab="$refs.confirmnewassword.focus()"
      >
    </div>
  </div>
</template>


<script>

export default{
  name: 'UserSettings',
  props: [ 'state' ],
  data(){
    return {
      newPassword: '',
      confirmNewPassword: '',
      currentPassword: '',
      savedPassword: false,
      showInvalid: false,
      avatarUpdateSuccessful: 0
    }
  },
  computed: {
    passwordsMatch(){
      return this.newPassword === this.confirmNewPassword
    },
    validate(){
      return this.passwordsMatch && this.newPassword && this.confirmNewPassword && this.currentPassword
    },
  },
  methods:{
    savePassword(){
      if(this.validate){
        let sendData = {userName: this.state.loggedinUserName, currentPassword: this.currentPassword, newPassword: this.newPassword}
        fetch(`${this.state.URLbase}/` + 'changePassword.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(data[0]){
            this.state.passhash = data[1]
            this.savedPassword = true
            this.currentPassword = this.newPassword = this.confirmNewPassword = ''
            this.state.setCookie()
          } else {
            this.showInvalid = true
          }
        })
      } else {
        this.savedPassword = false
        this.showInvalid = true
      }
    },
    closePrompt(){
      this.state.closePrompts()
    },
    updateAvatar(){
      let sendData = {userName: this.state.loggedinUserName, passhash: this.state.passhash, newAvatar: this.state.loggedInUser.avatar}
      fetch(`${this.state.URLbase}/` + 'updateAvatar.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          this.avatarUpdateSuccessful = 1
          //this.state.user.avatar = this.state.loggedInUser.avatar
          setTimeout(()=>this.avatarUpdateSuccessful = 0, 2000)
        } else {
          this.avatarUpdateSuccssful = -1
          setTimeout(()=>this.avatarUpdateSuccessful = 0, 2000)
        }
      })
    }
  },
  mounted(){
    this.$refs.currentpassword.focus()
  }
}
</script>

<style>
.UserSettings{
  position: absolute;
  width: 100vw;
  height: 100vh;
  z-index: 10000;
  top: 0;
  background: #102e;
  text-align: center;
}
.mainContainer{
  position: absolute;
  left: 50%;
  top: 50%;
  font-size: 24px;
  min-width: 320px;
  min-height: 200px;
  border-radius: 10px;
  background: #204;
  padding: 20px;
  color: #08f;
  transform: translate(-50%, -50%);
  border: 6px solid #60f3;
}
.inputTitle{
  text-align: left;
  width: 300px;
  margin-left: auto;
  margin-right: auto;
}
.inputContainer{
  padding: 10px;
}
.title{
  font-size: 2em;
}
.input{
  text-align: center;
  background: #0004;
  width: calc(100% - 5px);
  font-size: 16px;
  color: #ffa;
}
.input:focus{
  outline: none;
}
.toggleButton{
  position: absolute;
  transform: translate(-55%,-36px);
  background: #6df;
}
.disabledButton{
  color: #888;
  background: #333;
}
.editAvatarImgContainer{
  margin-top: 15px;
  width: 250px;
  height: 250px;
  margin-left: auto;
  margin-right: auto;
  background: linear-gradient(135deg, #333, #000)
}
.editAvatarImg{
  margin-top: 50%;
  transform: translateY(-50%);
  width: 100%;
  height: 100%;
  object-fit: contain;
}
.passwordFields{
  text-align: center;
  background: #0004;
  border: none;
  width: calc(100% - 5px);
  color: #ffa;
  font-size: 22px;
  max-width: 320px;
}
.passwordsDoNotMatch{
  background: #b446!important;
}
.passwordsMatch{
  background: #4b46!important;
}
.avatarInput{
  font-size: 16px!important;
}
.userSettingsButton{
  margin: 5px;
}
</style>

