<template>
  <v-card width="100%">
    <v-carousel
      height="905"
      :hide-delimiters="true"
      :show-arrows="false"
      :continuous="true"
      :cycle="true"
      :interval="7000"
    >
      <v-carousel-item v-for="(item_src, i) in indexBackground" :key="i">
        <v-img
          class=""
          width="100%"
          :src="require(`@/assets/${item_src}`)"
          height="905"
          ><v-overlay :value="overlay" :opacity="0.7" class="align-start-center"></v-overlay
        ></v-img>
      </v-carousel-item>
    </v-carousel>

    <div id="floatDiv">
      <div class="text-h1 font-weight-black">RentHub</div>
      <div class="text-h4 font-weight-thin">台灣最好的租屋網</div>
    </div>
  </v-card>
</template>

<script>
export default {
  name: "Home",
  data: () => ({
    overlay: true,
    indexBackground:[
      "olexandr-ignatov-w72a24brINI-unsplash.jpg",
      "chastity-cortijo-M8iGdeTSOkg-unsplash.jpg",
      "FBXuXp57eM0.jpg",
      "ralph-ravi-kayden-FqqiAvJejto-unsplash.jpg",
    ]
  }),
  components: {},
};
</script>

<style scope>
#floatDiv {
  position: absolute;
  top: 300px;
  left: 150px;
}
</style>