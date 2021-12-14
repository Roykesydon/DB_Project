<template>
  <v-app :style="cssProps">
    <v-app-bar app color="primary" dark>
      <a href="/" class="block-link">
        <div class="d-flex align-center">
          <v-img
            alt="Vuetify Logo"
            class="shrink mr-2"
            contain
            src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
            transition="scale-transition"
            width="40"
          />
          <span class="text-h4 white--text"> RentHub </span>
        </div>
      </a>
      <v-btn class="ma-1" plain href="/findRoom"> 找租屋處 </v-btn>

      <v-btn class="ma-1" href="/registerRoom" plain> 登記租屋 </v-btn>

      <v-spacer></v-spacer>
      <div v-if="alreadyLogin">
        <v-btn class="ma-1" plain href="/profile"> 個人資料 </v-btn>
        <v-btn class="ma-1" plain @click="signOut()"> 登出 </v-btn>
      </div>
      <div v-if="!alreadyLogin">
        <v-btn class="ma-1" plain @click="signInOrUpOverlay('login')">
          登入
        </v-btn>
        <v-btn class="ma-1" plain @click="signInOrUpOverlay('signUp')">
          註冊
        </v-btn>
      </div>
    </v-app-bar>

    <v-main>
      <v-overlay
        :z-index="10"
        :value="loginOverlay"
        @click.native="loginOverlay = false"
      >
        <v-card
          v-if="loginOrSignUp == 0"
          class="pa-10"
          min-width="400"
          :z-index="11"
          :ripple="false"
          @click.stop=""
        >
          <v-text-field
            class="ma-3"
            label="帳號"
            hide-details="auto"
          ></v-text-field>
          <v-text-field
            class="ma-3"
            label="密碼"
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
        </v-card>

        <v-card
          class="pa-10"
          min-width="400"
          :z-index="11"
          :ripple="false"
          @click.stop=""
          v-if="loginOrSignUp == 1"
        >
          <v-text-field
            class="ma-3"
            label="帳號"
            hide-details="auto"
          ></v-text-field>
          <v-text-field
            class="ma-3"
            label="電子郵件"
            hide-details="auto"
          ></v-text-field>
          <v-text-field
            class="ma-3"
            label="手機號碼"
            hide-details="auto"
          ></v-text-field>
          <v-text-field
            class="ma-3"
            label="密碼"
            hide-details="auto"
          ></v-text-field>
          <v-text-field
            class="ma-3"
            label="確認密碼"
            hide-details="auto"
          ></v-text-field>
          <div class="text-right">
            <v-btn
              class="white--text ma-3 mb-0"
              color="primary"
              @click.stop="loginOverlay = loginOverlay"
            >
              註冊
            </v-btn>
          </div>
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
      let check = true;
      if (check) {
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
        Vue.$toast.open({
          message: "登入失敗",
          type: "error",
          position: "top",
          duration: 3000,
          // all of other options may go here
        });
      }
    },
    signInOrUpOverlay(target) {
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
    loginOverlay: false,
    loginOrSignUp: 0,
    alreadyLogin: false,
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