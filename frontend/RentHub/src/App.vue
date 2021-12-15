<template>
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
          <span class="text-h4 ml-6 white--text"> RentHub </span>
        </div>
      </a>
      <v-btn class="ma-1" text href="/findRoom"> 找租屋處 </v-btn>

      <v-btn class="ma-1" href="/registerRoom" text> 登記租屋 </v-btn>

      <v-spacer></v-spacer>
      <div v-if="alreadyLogin">
        <v-btn class="ma-1" text href="/profile"> 個人資料 </v-btn>
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
        >
          <v-form ref="loginForm" v-model="signUpValid" lazy-validation>
            <v-text-field
              class="ma-3"
              label="信箱"
              counter
              maxlength="50"
              :rules="[rules.required, rules.email]"
              v-model="email"
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              label="密碼"
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
        >
          <v-form ref="signUpForm" v-model="signUpValid" lazy-validation>
            <v-text-field
              class="ma-3"
              label="帳號"
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
              hide-details="auto"
            ></v-text-field>
            <v-text-field
              class="ma-3"
              label="確認密碼"
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
export default {
  name: "App",
  mounted() {
    let elHtml = document.getElementsByTagName("html")[0];
    if (this.$vuetify.theme.dark) elHtml.style.backgroundColor = "#121212";
    if (this.$cookies.isKey("accessKey")) {
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
      this.$cookies.remove("accessKey");
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
      this.alreadyLogin = false;
    },
    login() {
      if (!this.$refs.loginForm.validate()) return;
      let userInfo = {
        password: this.password,
        email: this.email,
      };
      this.$axios
        .post("http://localhost:8000/api/login.php", userInfo)
        .then((res) => {
          if (res.data.success) {
            Vue.$toast.open({
              message: "登入成功! 正在跳轉頁面",
              type: "success",
              position: "top",
              duration: 2000,
              // all of other options may go here
            });
            this.loginOverlay = false;
            let _this = this;
            setTimeout(function () {
              _this.$router.push("findRoom");
            }, 2000);
            this.$cookies.set("accessKey", "25j_7Sl6xDq2Kc3ym0fmrSSk2xV2XkUkX");
            this.alreadyLogin = true;
          } else {
            let errorMessage = res.data.message;
            if (errorMessage == "Invalid Email Address!")
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
            this.$cookies.set("accessKey", "25j_7Sl6xDq2Kc3ym0fmrSSk2xV2XkUkX");
            this.alreadyLogin = true;
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
    rules: {
      required: (value) => !!value || "必填",
      name: (v) => {
        return (
          (v != null && v.length >= 3 && v.length <= 30) || "必須介在 3~30 個字"
        );
      },
      id: (v) => {
        return (
          (v != null && v.length >= 3 && v.length <= 50) || "必須介在 3~50 個字"
        );
      },
      email: (value) => {
        const pattern =
          /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return (value != null && pattern.test(value)) || "email 格式錯誤";
      },
      phoneNumber: (v) => {
        return (
          (v != null && v.length >= 10 && v.length <= 25) ||
          "必須介在 10~25 個字"
        );
      },
      password: (v) => {
        return (
          (v != null && v.length >= 8 && v.length <= 70) || "必須介在 8~70 個字"
        );
      },
    },
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