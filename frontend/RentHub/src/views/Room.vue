<template>
  <v-card
    class="my-5 mx-auto px-10 py-5 pt-0"
    max-width="80%"
    height="85%"
    outlined
    color="transparent"
  >
    <v-container class="fill-height">
      <v-row class="fill-height" height="100%" width="50%" style="width: 50%">
        <v-col md="6" v-if="!isRoomOwner" class="">
          <!-- <v-card color="primary"  class="text-h5 mb-2 mt-3 pa-10 pt-2 " style="float:left; z-index=-1;position: absolute;top:-10px; left:80px;" width="400px"><div class="text-truncate">{{ ownerRoomName }}</div></v-card> -->
          <div class="text-truncate text-h3 ma-3 ml-0">{{ ownerRoomName }}</div>
          <v-hover v-slot="{ hover }">
            <v-card elevation="24" style="z-index=10;" color="transparent">
              <v-tabs v-model="tab"  dark background-color="transparent">
                <v-tab :class="(tab==0)?'subtitle-1':'subtitle-2'"> 地圖 </v-tab>
                <v-tab :class="(tab==1)?'subtitle-1':'subtitle-2'"> 圖片 </v-tab>
              </v-tabs>

              <v-tabs-items v-model="tab">
                <v-tab-item class="fill-height">
                  <v-card width="100%" height="290px" class="text-center fill-height">
                    <div id="map2" class="fill-height"></div>
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-expand-transition
                    ><v-card
                      width="30%"
                      :color="hover ? '' : 'transparent'"
                      v-show="hover"
                      disabled
                      :dark="true"
                      :class="{
                        'show-card': hover,
                        'transparent--text': !hover,
                        'ma-5 pa-3 mx-auto': true,
                        
                      }"
                      style="
                        z-index: 10;
                        position: absolute;
                        top: 0px;
                        right: 10px;
                      "
                      elevation="0"
                      >點按圖片可切換全圖模式</v-card
                    ></v-expand-transition
                  >
                  <v-carousel
                    height="290px"
                    hide-delimiter-background
                    delimiter-icon="mdi-minus"
                    show-arrows-on-hover
                  >
                    <v-carousel-item
                      v-for="(item_src, i) in roomInfo.src"
                      :key="i"
                    >
                      <v-img
                        :src="item_src"
                        aspect-ratio="2.0"
                        class="my-auto"
                        :contain="roomImageContain"
                        @click="roomImageContain = !roomImageContain"
                        height="100%"
                      ></v-img>
                    </v-carousel-item>
                  </v-carousel>
                </v-tab-item>
              </v-tabs-items>
            </v-card>
          </v-hover>
          <div class="d-flex mb-0 sc3 mt-5">
            <v-text-field
              class=""
              label="地址"
              placeholder="地址"
              readonly
              outlined
              v-model="notOwnAddress"
            ></v-text-field>
            <v-text-field
              class="ml-5"
              label="城市"
              placeholder="城市"
              readonly
              outlined
              v-model="selectCity"
            ></v-text-field>
            <v-text-field
              class="ml-5"
              label="月租"
              placeholder="月租"
              readonly
              outlined
              v-model="ownerRoomCost"
            ></v-text-field>
          </div>
          <v-select
            :items="roomTags"
            item-text="text"
            attach
            chips
            readonly
            label="標籤"
            v-model="selectTags"
            multiple
            outlined
            class=""
          >
            <template v-slot:selection="{ item, index }">
              <v-chip color="primary">
                <span>{{ item }}</span>
              </v-chip>
            </template></v-select
          >
          <v-textarea
            no-resize
            outlined
            class="hide-scrollbar"
            name="description"
            label="介紹"
            readonly
            height="300px"
            v-model="notOwnDescription"
          ></v-textarea>
          <div class="d-flex align-end">
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" persistent max-width="470">
              <template v-slot:activator="{ on, attrs }">
                <v-btn color="primary" x-large dark v-bind="attrs" v-on="on">
                  屋主資訊
                </v-btn>
              </template>
              <v-card>
                <v-card-title class="text-h5"> 屋主資訊 </v-card-title>
                <div class="ma-5 mx-10">
                  姓名：{{ ownerName }}<br />
                  電話：{{ ownerPhoneNumber }}<br />
                  信箱：{{ ownerEmail }}<br />
                </div>
                <v-img
                  src="../assets/blank-profile-picture.png"
                  aspect-ratio="1"
                  height="150px"
                  width="150px"
                  id="profileImg"
                  class="mx-auto my-auto"
                  style="float:right; z-index=-1;position: absolute; top:20px; right:20px;"
                ></v-img>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="primary" text @click="dialog = false">
                    關閉
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </div>
        </v-col>

        <v-col md="6" v-if="isRoomOwner">
          <div class="text-h5 mb-2 mt-3">房間資料</div>
          <v-card width="100%" height="40%" class="text-center fill-height">
            <div id="map"></div>
          </v-card>

          <div class="d-flex mb-0 sc3 mt-5">
            <v-text-field
              class="mr-5"
              placeholder="地址"
              outlined
              clearable
              id="address"
            ></v-text-field>
            <v-btn
              x-large
              color="secondary mx-5 ml-0"
              @click="relocateMapWithAddress"
            >
              同步至地圖標記
            </v-btn>
            <v-btn color="secondary" x-large @click="relocateAddressWithMarker">
              同步至輸入地址
            </v-btn>
          </div>
          <div class="d-flex">
            <v-text-field
              class=""
              label="房屋名稱"
              placeholder="房屋名稱"
              id="roomName"
              :value="roomName"
              outlined
            ></v-text-field>
            <v-text-field
              class="ml-5"
              label="月租"
              id="roomCost"
              :value="roomCost"
              placeholder="月租"
              outlined
            ></v-text-field>
          </div>
          <div class="d-flex">
            <v-select
              :items="roomTags"
              item-text="text"
              attach
              chips
              label="標籤"
              v-model="selectTags"
              multiple
              outlined
              class="mr-5"
            >
              <template v-slot:selection="{ item, index }">
                <v-chip v-if="index == 0" color="primary">
                  <span>{{ item }}</span>
                </v-chip>
                <span v-if="index === 1" class="grey--text text-caption">
                  (+{{ selectTags.length - 1 }} others)
                </span>
              </template></v-select
            >
            <v-select
              :items="cities"
              item-text="text"
              attach
              chips
              required
              label="城市"
              :rules="[rules.required]"
              v-model="selectCity"
              outlined
              class="sc3"
            >
              <template v-slot:selection="{ item, index }">
                <v-chip v-if="index === 0" color="primary">
                  <span>{{ item }}</span>
                </v-chip>
              </template></v-select
            >
          </div>

          <v-divider></v-divider>

          <v-textarea
            no-resize
            outlined
            class="hide-scrollbar"
            name="description"
            label="介紹"
            height="300px"
            id="description"
            value="The Woodman set to work at once, and so sharp was his axe that the tree was soon chopped nearly through."
          ></v-textarea>

          <div class="d-flex">
            <v-file-input
              label="File input"
              outlined
              dense
              class="my-auto"
            ></v-file-input>
            <v-dialog v-model="dialog" persistent max-width="470">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  class="ma-5 my-auto mb-7 mr-0"
                  x-large
                  color="error"
                  v-bind="attrs"
                  v-on="on"
                >
                  刪除房屋
                </v-btn>
              </template>
              <v-card>
                <v-card-title class="text-h5">
                  您確定要刪除房屋嗎
                </v-card-title>
                <v-card-text
                  >按下確認後，資料不會留存，若是誤刪想恢復，必須重新填寫資料。
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="primary" text @click="deleteRoom()">
                    確認刪除
                  </v-btn>
                  <v-btn color="error" text @click="dialog = false">
                    取消
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-btn class="ma-5 my-auto mb-7 mr-0" x-large color="primary">
              更新資訊
            </v-btn>
          </div>
        </v-col>
        <v-col cols="1" class="mx-auto" height="150%">
          <v-divider vertical class="mx-5"></v-divider>
        </v-col>

        <v-col md="5" class="fill-width fill-height">
          <v-card class="pa-10" outlined color="transparent" height="1000px">
            <span class="text-h4 mb-4">同城市的其他房屋</span>
            <v-virtual-scroll
              :bench="benched"
              :items="items"
              height="620"
              item-height="330"
              class="sc3 pa-5"
            >
              <template v-slot:default="{ item }">
                <v-list-item class="ma-0">
                  <v-list-item-content>
                    <v-card
                      class="ma-0 mx-auto my-0"
                      elevation="20"
                      height="280px"
                      max-width="400"
                    >
                      <v-carousel
                        height="220"
                        hide-delimiter-background
                        delimiter-icon="mdi-minus"
                        show-arrows-on-hover
                      >
                        <v-carousel-item
                          v-for="(item_src, i) in item.src"
                          :key="i"
                        >
                          <v-img
                            :src="item_src"
                            aspect-ratio="2.0"
                            class="my-auto"
                          ></v-img>
                        </v-carousel-item>
                      </v-carousel>
                      <v-row>
                        <v-col cols="8">
                          <span>
                            <div class="text-h5 ma-3 mb-0 text-truncate">
                              {{ item.title }}
                            </div>
                            <div
                              class="
                                text-h7
                                ma-3
                                mt-0
                                grey--text
                                text--lighten-1
                              "
                            >
                              {{ item.cost }} 元/月
                            </div>
                          </span>
                        </v-col>
                        <v-col cols="4" class="my-auto mx-auto text-md-center">
                          <v-btn
                            class="ma-2 pr-5"
                            outlined
                            color="primary"
                            :href="getRoomRoute(item.roomID)"
                          >
                            查看詳情
                          </v-btn>
                        </v-col>
                      </v-row>
                    </v-card>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-virtual-scroll>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import * as CONFIG from "../../public/config";
export default {
  name: "RegisterRoom",

  mounted() {
    // // AIzaSyA07oLGCFBea5dZRB179KhJKhrbkUzTzpE
    // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.$config.GOOGLE-MAP-API-KEY )
    // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.googleMapAPI )
    console.log(this.$route.params["id"]);
    this.isRoomOwner = false;
    if (!this.isRoomOwner) {
      this.notOwnAddress = "測試地址";
      this.selectCity = "台北市";
      this.selectTags = [
        "Wi-Fi",
        "有線網路",
        "電視",
        "冰箱",
        "停車位",
        "冷氣",
        "洗衣機",
        "開伙",
        "養寵物",
      ];
      this.ownerRoomName = "新北市第一大豪宅";
      this.ownerRoomCost = 8600;
      this.notOwnDescription =
        "這屋子有夠棒，這屋子棒到一個不行，這屋子棒到不能再更棒了！要說為什麼，要從這屋子說起，這屋子雖然屋齡高，但老酒越陳越香！這屋子有夠棒，這屋子棒到一個不行，這屋子棒到不能再更棒了！要說為什麼，要從這屋子說起，這屋子雖然屋齡高，但老酒越陳越香這屋子有夠棒，這屋子棒到一個不行，這屋子棒到不能再更棒了！要說為什麼，要從這屋子說起，這屋子雖然屋齡高，但老酒越陳越香";
      this.ownerPhoneNumber = "085465465123";
      this.ownerEmail = "123123@123.asdas";
      this.ownerName = "大地主";
    } else {
      this.roomName = "測試名稱";
      // console.log(document.getElementById("roomName").value)
      // document.getElementById("roomName").text = "測試名稱"
    }
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://maps.googleapis.com/maps/api/js?key=" +
        CONFIG.googleMapAPI +
        "&libraries=places"
    );
    document.head.appendChild(recaptchaScript);

    initMap();

    console.log(this.tab)
  },

  data: () => ({
    roomImageContain: false,
    map: null,
    tab: null,
    notOwnAddress: "",
    address: "",
    ownerRoomName: "",
    ownerRoomCost: 0,
    markers: [],
    roomName: "",
    benched: 20,
    selectTags: [],
    rules: CONFIG.rules,
    isRoomOwner: true,
    notOwnDescription: "",
    dialog: false,
    ownerPhoneNumber: "",
    ownerEmail: "",
    ownerName: "",
    roomCost: 0,
    roomInfo: {
      title: "台北101超級無敵大全台最高豪宅",
      src: [
        "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
        "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
      ],
      address: "台北市信義區信義路五段7號",
      cost: 18000,
      capacity: 3,
      squareMeters: 11.0,
      roomID: 123456,
    },
    items: [
      {
        title: "台北101超級無敵大全台最高豪宅",
        src: [
          "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
          "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
          "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
          "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
        ],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: [
          "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
          "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
          "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
          "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
        ],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: [
          "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
          "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
          "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
          "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
        ],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
      {
        title: "台北101",
        src: ["https://cdn.vuetifyjs.com/images/cards/foster.jpg"],
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
        roomID: 123456,
      },
    ],
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
    ],
    cities: ["台北市", "基隆市", "桃園市"],
    selectCity: null,
    nightModeStyles: CONFIG.nightModeStyles,
  }),

  methods: {
    deleteRoom() {},  


    getRoomRoute(id) {
      return "/room/" + String(id);
    },
    /**
     * Handy functions to project lat/lng to pixel
     * Extracted from: https://developers.google.com/maps/documentation/javascript/examples/map-coordinates
     **/
    project(latLng) {
      var TILE_SIZE = 256;

      var siny = Math.sin((latLng.lat() * Math.PI) / 180);

      // Truncating to 0.9999 effectively limits latitude to 89.189. This is
      // about a third of a tile past the edge of the world tile.
      siny = Math.min(Math.max(siny, -0.9999), 0.9999);

      return new google.maps.Point(
        TILE_SIZE * (0.5 + latLng.lng() / 360),
        TILE_SIZE * (0.5 - Math.log((1 + siny) / (1 - siny)) / (4 * Math.PI))
      );
    },

    /**
     * Handy functions to project lat/lng to pixel
     * Extracted from: https://developers.google.com/maps/documentation/javascript/examples/map-coordinates
     **/
    getPixel(latLng, zoom) {
      var scale = 1 << zoom;
      var worldCoordinate = this.project(latLng);
      return new google.maps.Point(
        Math.floor(worldCoordinate.x * scale),
        Math.floor(worldCoordinate.y * scale)
      );
    },

    /**
     * Given a map, return the map dimension (width and height)
     * in pixels.
     **/
    getMapDimenInPixels(map) {
      var zoom = map.getZoom();
      var bounds = map.getBounds();
      var southWestPixel = this.getPixel(bounds.getSouthWest(), zoom);
      var northEastPixel = this.getPixel(bounds.getNorthEast(), zoom);
      return {
        width: Math.abs(southWestPixel.x - northEastPixel.x),
        height: Math.abs(southWestPixel.y - northEastPixel.y),
      };
    },

    /**
     * Given a map and a destLatLng returns true if calling
     * map.panTo(destLatLng) will be smoothly animated or false
     * otherwise.
     *
     * optionalZoomLevel can be optionally be provided and if so
     * returns true if map.panTo(destLatLng) would be smoothly animated
     * at optionalZoomLevel.
     **/
    willAnimatePanTo(map, destLatLng, optionalZoomLevel) {
      var dimen = this.getMapDimenInPixels(map);

      var mapCenter = map.getCenter();
      optionalZoomLevel = !!optionalZoomLevel
        ? optionalZoomLevel
        : map.getZoom();

      var destPixel = this.getPixel(destLatLng, optionalZoomLevel);
      var mapPixel = this.getPixel(mapCenter, optionalZoomLevel);
      var diffX = Math.abs(destPixel.x - mapPixel.x);
      var diffY = Math.abs(destPixel.y - mapPixel.y);

      return diffX < dimen.width && diffY < dimen.height;
    },

    /**
     * Returns the optimal zoom value when animating
     * the zoom out.
     *
     * The maximum change will be currentZoom - 3.
     * Changing the zoom with a difference greater than
     * 3 levels will cause the map to "jump" and not
     * smoothly animate.
     *
     * Unfortunately the magical number "3" was empirically
     * determined as we could not find any official docs
     * about it.
     **/
    getOptimalZoomOut(map, latLng, currentZoom) {
      if (this.willAnimatePanTo(map, latLng, currentZoom - 1)) {
        return currentZoom - 1;
      } else if (this.willAnimatePanTo(map, latLng, currentZoom - 2)) {
        return currentZoom - 2;
      } else {
        return currentZoom - 3;
      }
    },

    /**
     * Given a map and a destLatLng, smoothly animates the map center to
     * destLatLng by zooming out until distance (in pixels) between map center
     * and destLatLng are less than map width and height, then panTo to destLatLng
     * and finally animate to restore the initial zoom.
     *
     * optionalAnimationEndCallback can be optionally be provided and if so
     * it will be called when the animation ends
     **/
    smoothlyAnimatePanToWorkarround(
      map,
      destLatLng,
      optionalAnimationEndCallback
    ) {
      var initialZoom = map.getZoom(),
        listener;

      function zoomIn() {
        if (map.getZoom() < initialZoom) {
          map.setZoom(Math.min(map.getZoom() + 3, initialZoom));
        } else {
          google.maps.event.removeListener(listener);

          //here you should (re?)enable only the ui controls that make sense to your app
          map.setOptions({
            draggable: true,
            zoomControl: true,
            scrollwheel: true,
            disableDoubleClickZoom: false,
          });

          if (!!optionalAnimationEndCallback) {
            optionalAnimationEndCallback();
          }
        }
      }
      let _this = this;
      function zoomOut() {
        if (_this.willAnimatePanTo(map, destLatLng)) {
          google.maps.event.removeListener(listener);
          listener = google.maps.event.addListener(map, "idle", zoomIn);
          map.panTo(destLatLng);
        } else {
          map.setZoom(_this.getOptimalZoomOut(map, destLatLng, map.getZoom()));
        }
      }

      //here you should disable all the ui controls that your app uses
      map.setOptions({
        draggable: false,
        zoomControl: false,
        scrollwheel: false,
        disableDoubleClickZoom: true,
      });
      map.setZoom(this.getOptimalZoomOut(map, destLatLng, initialZoom));
      listener = google.maps.event.addListener(map, "idle", zoomOut);
    },

    smoothlyAnimatePanTo(map, destLatLng) {
      if (this.willAnimatePanTo(map, destLatLng)) {
        map.panTo(destLatLng);
      } else {
        this.smoothlyAnimatePanToWorkarround(map, destLatLng);
      }
    },

    relocateAddressWithMarker() {
      let marker = this.markers[0];
      let lat = marker.getPosition().lat();
      let lng = marker.getPosition().lng();
      const latlng = {
        lat: parseFloat(lat),
        lng: parseFloat(lng),
      };
      let _this = this;
      this.geocoder
        .geocode({ location: latlng })
        .then((response) => {
          if (response.results[0]) {
            console.log(response.results[0].formatted_address);
            _this.address = response.results[0].formatted_address;

            // _this.deleteMarkers();
            // _this.addMarker(latlng, _this.map)
            // infowindow.setContent(response.results[0].formatted_address);
            // infowindow.open(map, newMarker);
          } else {
            window.alert("No results found");
          }
        })
        .catch((e) => window.alert("Geocoder failed due to: " + e));
    },

    moveToOnlyMarker() {
      if (this.markers.length == 1) {
        this.smoothlyAnimatePanTo(this.map, this.markers[0].position);
        // this.map.panTo(this.markers[0].position)
      } else {
        console.log("error");
      }
    },

    addMarker(position, map) {
      const marker = new google.maps.Marker({
        position: position,
        map: map,
        draggable: true,
      });

      this.markers.push(marker);
    },

    // Sets the map on all markers in the array.
    setMapOnAll(map) {
      console.log(this.markers.length);
      for (let i = 0; i < this.markers.length; i++) {
        this.markers[i].setMap(map);
      }
    },

    // Removes the markers from the map, but keeps them in the array.
    hideMarkers() {
      this.setMapOnAll(null);
    },

    // Shows any markers currently in the array.
    showMarkers() {
      this.setMapOnAll(this.map);
    },

    // Deletes all markers in the array by removing references to them.
    deleteMarkers() {
      this.hideMarkers();
      this.markers = [];
    },

    relocateMapWithAddress() {
      console.log(this.tags);
      let location = null;
      var request = {
        query: this.address,
        fields: ["name", "geometry"],
      };
      var service = new google.maps.places.PlacesService(this.map);

      let _this = this;
      service.findPlaceFromQuery(request, function (results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          // console.log(results);
          console.log(results["0"]["geometry"]["location"]["lat"]());
          console.log(results["0"]["geometry"]["location"]["lng"]());
          location = {
            lat: results["0"]["geometry"]["location"]["lat"](),
            lng: results["0"]["geometry"]["location"]["lng"](),
          };
          // for (var i = 0; i < results.length; i++) {
          //   createMarker(results[i]);
          // }
          // map.setCenter(results[0].geometry.location);
          _this.deleteMarkers();
          _this.addMarker(location, _this.map);
          _this.showMarkers();
          _this.moveToOnlyMarker();
        } else {
          console.log("查無此處");
        }
      });
    },

    initMap() {
      // 預設顯示的地點：台北市政府親子劇場
      let location = {
        lat: 25.0374865, // 經度
        lng: 121.5647688, // 緯度
      };
      this.geocoder = new google.maps.Geocoder();
      // 建立地圖

      let renderMap = "map";
      if (!this.isRoomOwner) renderMap = "map2";
      this.map = new google.maps.Map(document.getElementById(renderMap), {
        center: location, // 中心點座標
        zoom: 16, // 1-20，數字愈大，地圖愈細：1是世界地圖，20就會到街道
        /*
          roadmap 顯示默認道路地圖視圖。
          satellite 顯示 Google 地球衛星圖像。
          hybrid 顯示正常和衛星視圖的混合。
          terrain 顯示基於地形信息的物理地圖。
        */
        mapTypeId: "roadmap",
        disableDefaultUI: true,
      });

      // 放置marker
      // let marker = new google.maps.Marker({
      //   position: location,
      //   map: this.map,
      // });
      this.addMarker(location, this.map);

      this.map.setOptions({
        styles: CONFIG.nightModeStyles,
      });
    },

    async geoCoding(address) {
      // const result = document.querySelector('.result');

      var request = {
        query: address,
        fields: ["name", "geometry"],
      };
      var service = new google.maps.places.PlacesService(this.map);

      service.findPlaceFromQuery(request, function (results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          // console.log(results);
          console.log(results["0"]["geometry"]["location"]["lat"]());
          console.log(results["0"]["geometry"]["location"]["lng"]());
          return {
            lat: results["0"]["geometry"]["location"]["lat"](),
            lng: results["0"]["geometry"]["location"]["lng"](),
          };
          // for (var i = 0; i < results.length; i++) {
          //   createMarker(results[i]);
          // }
          // map.setCenter(results[0].geometry.location);
        } else {
          // console.log("查無此處");
          return null;
        }
      });
    },
    test() {
      console.log(this.tab)
      let location = {
        lat: 25.1374865, // 經度
        lng: 121.5647688, // 緯度
      };
      let marker = new google.maps.Marker({
        position: location,
        map: this.map,
      });
    },
  },
  created: function () {
    window.addEventListener("load", () => {
      this.initMap();
    });
  },
};
</script>

<style  scoped>
.show-card {
  color: rgba(255, 255, 255, 1) !important;
}

#map {
  height: 100%;
  width: 100%;
}

html {
  overflow: hidden !important;
}

.v-card {
  display: flex !important;
  flex-direction: column;
}

.v-card__text {
  flex-grow: 1;
  overflow: auto;
}

/* Scroll 3 */
.sc3::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}
.sc3::-webkit-scrollbar-track {
  background-color: transparent;
  border-radius: 10px;
}
.sc3::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.4);
  border-radius: 10px;
}

#profileImg {
  border-radius: 50%;
}
</style>