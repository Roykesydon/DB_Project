<template>
  <!-- 我對於把幾乎所有東西都寫在 View 感到抱歉，這專案給的時間有點不足 -->
  <v-app :style="cssProps">
    <v-app-bar app color="primary" dark>
      <a href="/" class="block-link">
        <!-- <div class="d-flex align-center">
          <v-img
            alt="Vuetify Logo"
            class="shrink mr-2"
            contain
            src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
            transition="scale-transition"
            width="40"
          /> -->
        <span class="text-h4 ml-4 mr-2 white--text"> RentHub </span>
        <!-- </div> -->
      </a>
      <v-btn class="ma-1" text href="/findRoom"> 找租屋處 </v-btn>

      <v-btn class="ma-1" href="/registerRoom" text> 登記租屋 </v-btn>

      <v-spacer></v-spacer>
      <div v-if="alreadyLogin">
        <v-btn class="ma-1" text :href="getProfileUrl()"> 個人資料 </v-btn>
        <v-btn class="ma-1" text @click="signOut()"> 登出 </v-btn>
      </div>
      <div v-if="!alreadyLogin">
        <v-btn class="ma-1" text @click="signInOrUpOverlay('login')">
          登入
        </v-btn>
        <v-btn class="ma-1" text @click="signInOrUpOverlay('signUp')">
          註冊
        </v-btn>
      </div>
    </v-app-bar>

    <v-main>
      <v-overlay
        :z-index="10"
        :value="loginOverlay"
        @mousedown.native="loginOverlay = false"
      >
        <v-card
          v-if="loginOrSignUp == 0"
          class="pa-10"
          min-width="400"
          :z-index="11"
          :ripple="false"
          @mousedown.stop=""
          color="#292929"
        >
          <v-form ref="loginForm" v-model="signUpValid" lazy-validation>
            <v-text-field
              class="ma-3"
              label="帳號"
              prepend-icon="mdi-account-circle-outline"
              counter
              maxlength="50"
              :rules="[rules.required, rules.id]"
              v-model="user_ID"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              label="密碼"
              prepend-icon="mdi-lock"
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword ? 'text' : 'password'"
              @click:append="showPassword = !showPassword"
              counter
              :rules="[rules.required, rules.password]"
              maxlength="70"
              v-model="password"
              hide-details="auto"
            ></v-text-field>
            <div class="text-right">
              <v-btn
                class="white--text ma-3 mb-0"
                color="primary"
                @click.stop="login()"
              >
                登入
              </v-btn>
            </div>
          </v-form>
        </v-card>

        <v-card
          class="pa-10"
          min-width="400"
          :z-index="11"
          :ripple="false"
          @mousedown.stop=""
          v-if="loginOrSignUp == 1"
          color="#292929"
        >
          <v-form ref="signUpForm" v-model="signUpValid" lazy-validation>
            <v-text-field
              class="ma-3"
              label="帳號"
              prepend-icon="mdi-account-circle-outline"
              v-model="account"
              counter
              :rules="[rules.required, rules.id]"
              maxlength="50"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              v-model="name"
              label="姓名"
              prepend-icon="mdi-file-document-outline"
              counter
              :rules="[rules.required, rules.name]"
              maxlength="30"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              v-model="email"
              counter
              label="電子郵件"
              prepend-icon="mdi-email-outline"
              maxlength="50"
              :rules="[rules.required, rules.email]"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              v-model="phoneNumber"
              counter
              :rules="[rules.required, rules.phoneNumber]"
              maxlength="25"
              label="手機號碼"
              prepend-icon="mdi-cellphone"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              v-model="password"
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword ? 'text' : 'password'"
              @click:append="showPassword = !showPassword"
              counter
              :rules="[rules.required, rules.password]"
              maxlength="70"
              label="密碼"
              prepend-icon="mdi-lock"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              label="確認密碼"
              prepend-icon="mdi-lock"
              :append-icon="showCheckPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showCheckPassword ? 'text' : 'password'"
              @click:append="showCheckPassword = !showCheckPassword"
              counter
              :rules="[
                rules.required,
                password === checkPassword || '和密碼不同',
              ]"
              maxlength="70"
              v-model="checkPassword"
              hide-details="auto"
            ></v-text-field>
            <div class="text-right">
              <v-btn
                class="white--text ma-3 mb-0"
                color="primary"
                @click.stop="signUp()"
              >
                註冊
              </v-btn>
            </div>
          </v-form>
        </v-card>
      </v-overlay>
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
import Vue from "vue";
import * as CONFIG from "../public/config";

export default {
  name: "App",
  mounted() {
    let elHtml = document.getElementsByTagName("html")[0];
    if (this.$vuetify.theme.dark) elHtml.style.backgroundColor = "#121212";
    if (this.$cookies.isKey("token")) {
      this.alreadyLogin = true;
    }
  },
  computed: {
    cssProps() {
      var themeColors = {};
      Object.keys(this.$vuetify.theme.themes.light).forEach((color) => {
        themeColors[`--v-${color}`] = this.$vuetify.theme.themes.dark[color];
      });
      return themeColors;
    },
  },
  methods: {
    signOut() {
      this.$cookies.remove("token");
      this.$cookies.remove("alreadyLogin");
      this.$cookies.remove("user_ID");
      this.alreadyLogin = false;
      Vue.$toast.open({
        message: "登出成功! 重新導至首頁",
        type: "success",
        position: "top",
        duration: 3000,
        // all of other options may go here
      });
      let _this = this;
      setTimeout(function () {
        _this.$router.push("/");
      }, 2000);    
    },
    getProfileUrl() {
      return "/profile/" + this.$cookies.get("user_ID");
    },
    login() {
      if (!this.$refs.loginForm.validate()) return;
      let userInfo = {
        password: this.password,
        user_ID: this.user_ID,
      };
      this.$axios
        .post("http://localhost:8000/api/login.php", userInfo)
        .then((res) => {
          if (res.data.success) {
            console.log(res.data);
            Vue.$toast.open({
              message: "登入成功!",
              type: "success",
              position: "top",
              duration: 2000,
              // all of other options may go here
            });
            this.loginOverlay = false;
            // let _this = this;
            // setTimeout(function () {
            //   _this.$router.push("findRoom");
            // }, 2000);
            this.$cookies.set("token", res.data.token);
            this.$cookies.set("user_ID", this.user_ID);
            this.alreadyLogin = true;
            this.$cookies.set("alreadyLogin", true);
          } else {
            let errorMessage = res.data.message;
            if (errorMessage == "Invalid user_ID!")
              errorMessage = "信箱不存在";
            else if (errorMessage == "Invalid Password!")
              errorMessage = "密碼錯誤";
            Vue.$toast.open({
              message: errorMessage,
              type: "error",
              position: "top",
              duration: 4000,
              // all of other options may go here
            });
            console.log("error!");
            console.log(res);
            console.log(res.data.message);
          }
        })
        .catch((error) => {
          Vue.$toast.open({
            message: "登入失敗",
            type: "error",
            position: "top",
            duration: 3000,
            // all of other options may go here
          });
          console.log("network error!");
          console.error(error);
        });
    },
    signUp() {
      if (!this.$refs.signUpForm.validate()) return;
      // console.log(this.$refs.signUpForm.validate());
      // if (this.signUpValid == false) return;
      let userInfo = {
        name: this.name,
        id: this.account,
        password: this.password,
        email: this.email,
        phoneNumber: this.phoneNumber,
      };
      // console.log(userInfo);
      this.$axios
        .post("http://localhost:8000/api/register.php", userInfo)
        .then((res) => {
          if (res.data.success) {
            console.log("success!");
            Vue.$toast.open({
              message: "註冊成功! 正在跳轉頁面",
              type: "success",
              position: "top",
              duration: 2000,
              // all of other options may go here
            });
            this.signUpOverlay = false;
            let _this = this;
            setTimeout(function () {
              _this.$router.push("findRoom");
            }, 2000);
            // this.$cookies.set("token", "25j_7Sl6xDq2Kc3ym0fmrSSk2xV2XkUkX");
            // this.alreadyLogin = true;
            console.table(res.data);
          } else {
            console.log("error!");
            let errorMessage = res.data.message;
            if (errorMessage == "This E-mail already in use!")
              errorMessage = "Email 已經被使用";
            else if (errorMessage == "This user_ID already in use!")
              errorMessage = "帳號已經被使用";

            Vue.$toast.open({
              message: errorMessage,
              type: "error",
              position: "top",
              duration: 4000,
              // all of other options may go here
            });
            console.log(res);
            console.log(res.data.message);
          }
        })
        .catch((error) => {
          console.log("network error!");
          console.error(error);
        });
    },
    clearPasswordInfo() {
      this.password = null;
      this.checkPassword = null;
    },
    signInOrUpOverlay(target) {
      this.clearPasswordInfo();
      if (target === "login") {
        this.loginOverlay = true;
        this.loginOrSignUp = 0;
      } else {
        this.loginOverlay = true;
        this.loginOrSignUp = 1;
      }
    },
  },
  data: () => ({
    rules: CONFIG.rules,
    signUpValid: false,
    loginOverlay: false,
    loginOrSignUp: 0,
    alreadyLogin: false,
    account: null,
    email: null,
    phoneNumber: null,
    password: null,
    checkPassword: null,
    name: null,
    showPassword: false,
    showCheckPassword: false,
    user_ID:"",
  }),
};
</script>

<style scope>
textarea::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}
textarea::-webkit-scrollbar-track {
  background-color: transparent;
  border-radius: 10px;
}
textarea::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.4);
  border-radius: 10px;
}
</style>