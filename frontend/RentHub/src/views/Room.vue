<template>
  <v-card
    class="my-5 mx-auto px-10 py-5"
    max-width="80%"
    height="85%"
    outlined
    color="transparent"
  >
    <v-container class="fill-height">
      <v-row class="fill-height" height="100%" width="50%" style="width: 50%">
        <v-col md="6">
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
              v-model="address"
            ></v-text-field>
            <v-btn x-large color="primary" @click="relocateMapWithAddress">
              與地圖同步
            </v-btn>
          </div>
          <div>
            <v-select
              :items="roomTags"
              item-text="text"
              attach
              chips
              label="tag"
              multiple
              outlined
            ></v-select>
          </div>

          <v-divider></v-divider>

          <v-textarea
            no-resize
            outlined
            class="hide-scrollbar"
            name="description"
            label="介紹"
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
            <v-btn class="ma-5 my-auto mb-7 mr-0" x-large color="primary">
              上傳圖片
            </v-btn>
          </div>

          <div class="d-flex align-end" style="">
            <v-spacer></v-spacer>
            <v-btn color="primary" x-large> 登記房屋 </v-btn>
          </div>
        </v-col>
        <v-col cols="1" class="mx-auto" height="auto">
        <v-divider vertical class="mx-5"></v-divider>
        </v-col>
        <v-col md="5" class="fill-width fill-height">
          <v-card class="pa-10" outlined color="transparent">
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
    let recaptchaScript = document.createElement("script");
    // // AIzaSyA07oLGCFBea5dZRB179KhJKhrbkUzTzpE
    // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.$config.GOOGLE-MAP-API-KEY )
    // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.googleMapAPI )
    recaptchaScript.setAttribute(
      "src",
      "https://maps.googleapis.com/maps/api/js?key=" +
        CONFIG.googleMapAPI +
        "&libraries=places"
    );
    document.head.appendChild(recaptchaScript);
    initMap();
  },

  data: () => ({
    map: null,
    address: "",
    markers: [],
    benched: 20,
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
      {
        text: "hello",
      },
      {
        text: "run",
      },
      {
        text: "null",
      },
      {
        text: "hell",
      },
      {
        text: "helo",
      },
      {
        text: "ello",
      },
      {
        text: "hello2",
      },
      {
        text: "hello3",
      },
      {
        text: "hello4",
      },
      {
        text: "hello5",
      },
      {
        text: "hello6",
      },
      {
        text: "hello7",
      },
    ],
    nightModeStyles: [
      {
        elementType: "geometry",
        stylers: [
          {
            color: "#1d2c4d",
          },
        ],
      },
      {
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#8ec3b9",
          },
        ],
      },
      {
        elementType: "labels.text.stroke",
        stylers: [
          {
            color: "#1a3646",
          },
        ],
      },
      {
        featureType: "administrative.country",
        elementType: "geometry.stroke",
        stylers: [
          {
            color: "#4b6878",
          },
        ],
      },
      {
        featureType: "administrative.land_parcel",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#64779e",
          },
        ],
      },
      {
        featureType: "administrative.province",
        elementType: "geometry.stroke",
        stylers: [
          {
            color: "#4b6878",
          },
        ],
      },
      {
        featureType: "landscape.man_made",
        elementType: "geometry.stroke",
        stylers: [
          {
            color: "#334e87",
          },
        ],
      },
      {
        featureType: "landscape.natural",
        elementType: "geometry",
        stylers: [
          {
            color: "#023e58",
          },
        ],
      },
      {
        featureType: "poi",
        elementType: "geometry",
        stylers: [
          {
            color: "#283d6a",
          },
        ],
      },
      {
        featureType: "poi",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#6f9ba5",
          },
        ],
      },
      {
        featureType: "poi",
        elementType: "labels.text.stroke",
        stylers: [
          {
            color: "#1d2c4d",
          },
        ],
      },
      {
        featureType: "poi.park",
        elementType: "geometry.fill",
        stylers: [
          {
            color: "#023e58",
          },
        ],
      },
      {
        featureType: "poi.park",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#3C7680",
          },
        ],
      },
      {
        featureType: "road",
        elementType: "geometry",
        stylers: [
          {
            color: "#304a7d",
          },
        ],
      },
      {
        featureType: "road",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#98a5be",
          },
        ],
      },
      {
        featureType: "road",
        elementType: "labels.text.stroke",
        stylers: [
          {
            color: "#1d2c4d",
          },
        ],
      },
      {
        featureType: "road.highway",
        elementType: "geometry",
        stylers: [
          {
            color: "#2c6675",
          },
        ],
      },
      {
        featureType: "road.highway",
        elementType: "geometry.stroke",
        stylers: [
          {
            color: "#255763",
          },
        ],
      },
      {
        featureType: "road.highway",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#b0d5ce",
          },
        ],
      },
      {
        featureType: "road.highway",
        elementType: "labels.text.stroke",
        stylers: [
          {
            color: "#023e58",
          },
        ],
      },
      {
        featureType: "transit",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#98a5be",
          },
        ],
      },
      {
        featureType: "transit",
        elementType: "labels.text.stroke",
        stylers: [
          {
            color: "#1d2c4d",
          },
        ],
      },
      {
        featureType: "transit.line",
        elementType: "geometry.fill",
        stylers: [
          {
            color: "#283d6a",
          },
        ],
      },
      {
        featureType: "transit.station",
        elementType: "geometry",
        stylers: [
          {
            color: "#3a4762",
          },
        ],
      },
      {
        featureType: "water",
        elementType: "geometry",
        stylers: [
          {
            color: "#0e1626",
          },
        ],
      },
      {
        featureType: "water",
        elementType: "labels.text.fill",
        stylers: [
          {
            color: "#4e6d70",
          },
        ],
      },
    ],
  }),

  methods: {
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
        position,
        map,
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

      // 建立地圖
      this.map = new google.maps.Map(document.getElementById("map"), {
        center: location, // 中心點座標
        zoom: 16, // 1-20，數字愈大，地圖愈細：1是世界地圖，20就會到街道
        /*
          roadmap 顯示默認道路地圖視圖。
          satellite 顯示 Google 地球衛星圖像。
          hybrid 顯示正常和衛星視圖的混合。
          terrain 顯示基於地形信息的物理地圖。
        */
        mapTypeId: "roadmap",
      });

      // 放置marker
      // let marker = new google.maps.Marker({
      //   position: location,
      //   map: this.map,
      // });
      this.addMarker(location, this.map);

      this.map.setOptions({
        styles: this.nightModeStyles,
      });
    },
    async geoCoding(address) {
      // const result = document.querySelector('.result');

      // fetch('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=台北市南港區忠孝東路七段576號&inputtype=textquery&fields=formatted_address,name,rating,opening_hours,geometry&key='+this.googleMapAPI, {mode: 'no-cors'})
      //   .then(response =>{
      //     console.log(response)
      //     return  response.json();//解析成一個json 物件
      //   }).then((jsonData) => {
      //     console.log(jsonData);
      //   }).catch((err) => {
      //     console.log('錯誤:', err);
      //   });
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
</style>