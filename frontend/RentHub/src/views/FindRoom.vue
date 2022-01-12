<template>
  <div>
    <link
      rel="stylesheet"
      href="https://unpkg.com/vue-range-component@1.0.3/dist/vue-range-slider.min.css"
    />
    <div
      class="my-15 mx-auto px-10 py-5 mb-0 pb-0 pt-0"
      style="max-width: 1300px"
    >
      <div class="mx-10 pa-2 my-0 pb-0">
        <v-container>
          <div class="text-h5 mb-1">在此搜尋</div>
          <div class="d-flex mb-0">
            <v-text-field
              class=""
              placeholder="關鍵字"
              outlined
              clearable
              id="keyWord"
              :value="keyWord"
              style="height: 100px"
            ></v-text-field>
            <v-select
              :items="cities"
              item-text="text"
              attach
              chips
              label="城市"
              v-model="selectCities"
              multiple
              outlined
              class="sc3"
              style="max-width: 220px; width: 220px"
            >
              <template v-slot:selection="{ item, index }">
                <v-chip v-if="index === 0" color="primary">
                  <span>{{ item }}</span>
                </v-chip>
                <span v-if="index === 1" class="grey--text text-caption">
                  (+{{ selectCities.length - 1 }} others)
                </span>
              </template></v-select
            >
            <v-select
              :items="roomTags"
              item-text="text"
              attach
              chips
              label="標籤"
              v-model="selectTags"
              multiple
              outlined
              class=""
              style="max-width: 220px; width: 220px"
            >
              <template v-slot:selection="{ item, index }">
                <v-chip v-if="index === 0" color="primary">
                  <span>{{ item }}</span>
                </v-chip>
                <span v-if="index === 1" class="grey--text text-caption">
                  (+{{ selectTags.length - 1 }} others)
                </span>
              </template></v-select
            >
            <VDropdown>
              <v-btn
                x-large
                height="56px"
                color="#424242"
                outlined
                elevation="0"
              >
                <div id="priceButtonText">
                  $ {{ priceRange[0] }} ~ {{ priceRange[1] }}
                </div>
              </v-btn>
              <template #popper style="width=500px;">
                <div>
                  <vue-range-slider
                    v-model="priceRange"
                    width="500px"
                    :min="priceMin"
                    :max="priceMax"
                    :lazy="true"
                    :enable-cross="false"
                    :bg-style="bgStyle"
                    :tooltip-style="tooltipStyle"
                    :process-style="processStyle"
                  ></vue-range-slider>
                </div>
              </template>
            </VDropdown>

            <v-btn
              dark
              x-large
              class="ml-10"
              color="primary"
              height="55px"
              @click="searchWithFilter"
            >
              搜尋
            </v-btn>
          </div>
        </v-container>
      </div>
    </div>
    <div class="ma-0 pa-5">
      <v-row>
        <v-col cols="3" v-for="(item, i) in items" :key="i" class="pa-7 py-5"
          ><div>
            <v-card
              class="ma-10 mx-auto my-0 rounded-xl"
              elevation="20"
              height="290px"
              max-width="500"
            >
              <v-carousel
                height="200"
                hide-delimiter-background
                delimiter-icon="mdi-minus"
                show-arrows-on-hover
              >
                <v-carousel-item v-for="(item_src, i) in item.src" :key="i">
                  <v-img
                    :src="item_src"
                    aspect-ratio="2.0"
                    class="my-auto"
                  ></v-img>
                </v-carousel-item>
              </v-carousel>
              <v-row>
                <v-col cols="7">
                  <span>
                    <div class="text-h6 ma-3 mb-0 text-truncate">
                      {{ item.title }}
                    </div>
                    <div class="text-h7 ma-3 mt-0 grey--text text--lighten-1">
                      {{ item.cost }} 元/月
                    </div>
                  </span>
                </v-col>
                <v-col cols="5" class="my-auto mx-auto text-right">
                  <v-btn
                    class="ma-2 pr-5 mr-5"
                    outlined
                    color="secondary"
                    :href="getRoomRoute(item.roomID)"
                  >
                    查看詳情
                  </v-btn>
                </v-col>
              </v-row>
            </v-card>
          </div></v-col
        >
      </v-row>
    </div>
    <mugen-scroll :handler="fetchData" :should-handle="!loading">
      <div class="text-center ma-10">
        <v-progress-circular
          v-if="loading"
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
    </mugen-scroll>
  </div>
</template>

<script>
import MugenScroll from "vue-mugen-scroll";
import VueRangeSlider from "vue-range-component";
import Slider from "@vueform/slider/dist/slider.vue2.js";
export default {
  data: () => ({
    keyWord: null,
    loading: false,
    page: 1,
    selectTags: [],
    selectCities: [],
    priceRange: [0, 20000],
    priceMin: 0,
    priceMax: 20000,
    value: 20,
    fetchIndex: 0,
    roomTags: [
      "Wi-Fi",
      "有線網路",
      "電視",
      "冰箱",
      "停車位",
      "冷氣",
      "洗衣機",
      "開伙",
      "養寵物",
      "電梯",
    ],
    cities: [
      "新北市",
      "臺北市",
      "桃園市",
      "臺中市",
      "臺南市",
      "高雄市",
      "新竹縣",
      "苗栗縣",
      "彰化縣",
      "南投縣",
      "雲林縣",
      "嘉義縣",
      "屏東縣",
      "宜蘭縣",
      "花蓮縣",
      "臺東縣",
      "澎湖縣",
      "金門縣",
      "連江縣",
      "基隆市",
      "新竹市",
      "嘉義市",
    ],
    items: [],
  }),
  mounted() {},
  computed: {},
  created: function () {
    VueRangeSlider.methods.handleKeyup = () => console.log;
    VueRangeSlider.methods.handleKeydown = () => console.log;
    this.bgStyle = {
      backgroundColor: "#555",
      boxShadow: "inset 0.5px 0.5px 3px 1px rgba(0,0,0,.36)",
    };
    this.tooltipStyle = {
      backgroundColor: this.$vuetify.theme.themes.dark.primary,
      borderColor: this.$vuetify.theme.themes.dark.primary,
    };
    this.processStyle = {
      backgroundColor: this.$vuetify.theme.themes.dark.primary,
    };
  },
  methods: {
    getRoomRoute(id) {
      return "/room/" + String(id);
    },
    searchWithFilter() {
      this.items = [];
      this.keyWord = document.getElementById("keyWord").value;
      // console.log({
      //   keyWord: this.keyWord,
      //   priceRange: this.priceRange,
      //   selectTags: this.selectTags,
      //   selectCitites: this.selectCities,
      // });
      this.items = [];
      this.fetchIndex = 0;
      this.fetchData();
    },
    sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },
    fetchData() {
      let _this = this;
      this.loading = true;
      console.log({
        index: this.fetchIndex,
        keyword: this.keyWord,
        cost: this.priceRange.join(','),
        tag: this.selectTags.join(','),
        city: this.selectCities.join(','),
      });

      let tmp =    {
        index: this.fetchIndex++,
        keyword: this.keyWord,
        cost: this.priceRange.join(','),
        tag: null,
        city: null,
      };

      if(this.selectCities.length!=0)
        tmp.city = this.selectCities.join(',');
      if(this.selectTags.length!=0)
        tmp.tag = this.selectTags.join(',');

      this.$axios
        .get("http://localhost:8000/api/room/readRoomBySearch.php", {
          params: tmp,
        })
        .then((res) => {
          console.log(res.data.records);
          // this.items = [];
          let data = res.data.records;
          for (let i = 0; i < data.length; i++) {
            let tmp = {
              title: data[i].room_name,
              src: [],
              address: data[i].address,
              cost: data[i].cost,
              capacity: data[i].live_number,
              roomID: data[i].room_ID,
            };
            for (let j = 0; j < data[i].URLs.length; j++) {
              tmp.src.push(
                "http://localhost:8000/api/room/getRoomPicture.php?user_ID=" +
                  data[i].user_ID +
                  "&URL=" +
                  data[i].URLs[j]
              );
            }
            _this.items.push(tmp);
          }
          _this.loading = false;
        })
        .catch((error) => {
          console.log("network error!");
          console.error(error);
        });

      console.log("update");
    },

    getPicture(index) {
      return "https://picsum.photos/1920/1080?" + index + String(Date.now());
    },
  },
  components: { MugenScroll, VueRangeSlider, Slider },
  // <style src="@vueform/slider/themes/default.css"></style>
};
</script>
<style scope>
div::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}
div::-webkit-scrollbar-track {
  background-color: rgba(30, 30, 30, 1);
  /* border-radius: 10px; */
}
div::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.4);
  border-radius: 10px;
}

.v-text-field .v-input__control .v-input__slot {
  height: 50px !important;
  display: flex !important;
  align-items: center !important;
}

#priceButtonText {
  color: var(--v-primary) !important;
}
</style>
