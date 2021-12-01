<template>
  <v-app>
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
        <span class="text-h4 white--text"  > RentHub </span>
      </div>
      </a>
      <v-btn  class="ma-1" plain href="/findRoom">
        找租屋處
      </v-btn>

      <v-btn class="ma-1" href="/registerRoom" plain>
        登記租屋
      </v-btn>

      <v-spacer></v-spacer>

      <v-btn  class="ma-1" plain href="/profile"> 個人資料 </v-btn>
      <v-btn  class="ma-1" plain> 登出 </v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: "App",
  mounted() {
    let elHtml = document.getElementsByTagName("html")[0];
    if (this.$vuetify.theme.dark) elHtml.style.backgroundColor = "#121212";
  },
  data: () => ({
    //
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