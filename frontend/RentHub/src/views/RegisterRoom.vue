<template>
  <v-card
    class="my-15 mx-auto px-10 py-5"
    max-width="70%"
    height="85%"
    color="transparent"
    outlined
  >
    <v-container class="fill-height">
      <v-row class="fill-height" height="100%" width="50%" style="width: 50%">
        <v-col md="7">
          <v-card width="100%" class="text-center fill-height">
            <div id="map"></div>
          </v-card>
        </v-col>
        <v-col md="5" class="fill-width">
          <v-form ref="form" v-model="valid" lazy-validation>
            <div class="text-h5 mb-2">填寫資料</div>
            <div class="d-flex mb-0">
              <v-text-field
                class="mb-0 pb-0"
                placeholder="地址"
                outlined
                clearable
                v-model="address"
                :rules="[rules.required, rules.address]"
              ></v-text-field>
            </div>
            <div class="d-flex mb-5">
              <v-spacer></v-spacer>
              <v-btn color="primary mx-5" @click="relocateMapWithAddress">
                同步至地圖標記
              </v-btn>
              <v-btn color="primary" @click="relocateAddressWithMarker">
                同步至輸入地址
              </v-btn>
            </div>
            <div class="d-flex">
              <v-text-field
                class=""
                label="名稱"
                placeholder="名稱"
                outlined
                :rules="[rules.required, rules.roomName]"
                v-model="roomName"
              ></v-text-field>
              <v-text-field
                class=""
                label="月租"
                placeholder="月租"
                :rules="[rules.required, rules.cost]"
                outlined
                v-model="roomCost"
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
                class=""
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
              <div>
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
            </div>

            <v-divider></v-divider>

            <v-textarea
              no-resize
              outlined
              label="介紹"
              v-model="description"
            ></v-textarea>

            <div class="d-flex">
              <v-file-input
                label="上傳照片(可選取多張)"
                outlined
                dense
                multiple
                class="my-auto"
                :rules="[rules.arrayRequired,rules.files]"
                accept="image/png, image/jpeg, image/bmp"
                v-model="uploadPictures"
                prepend-icon="mdi-camera"
              ></v-file-input>
              <!-- <v-btn class="ma-5 my-auto mb-7" x-large color="primary">
              上傳圖片
            </v-btn> -->
            </div>

            <div class="d-flex align-end" style="height: 27%">
              <v-spacer></v-spacer>
              <v-dialog v-model="dialog" persistent max-width="470">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn color="primary" x-large dark v-bind="attrs" v-on="on">
                    登記房屋
                  </v-btn>
                </template>
                <v-card>
                  <v-card-title class="text-h5">
                    你是否已經確認您填的地址可以對映到 google 地圖上？
                  </v-card-title>
                  <v-card-text
                    >您的房間位置能否正確顯示在 google
                    地圖上，對您和您的客戶來說至關重要。麻煩務必同步後親自確認。
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text @click="registerRoom()">
                      確認無誤
                    </v-btn>
                    <v-btn color="error" text @click="dialog = false">
                      取消
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </div>
          </v-form>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import * as CONFIG from "../../public/config";
import Vue from "vue";
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
    roomName: null,
    roomCost: null,
    dialog: false,
    map: null,
    geocoder: null,
    address: "",
    markers: [],
    selectTags: [],
    uploadPictures: [],
    valid: false,
    description: "",
    rules: CONFIG.rules,
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
    selectCity: null,
    lat: 0,
    lng: 0,
  }),

  methods: {
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
    registerRoom() {
      this.dialog = false;
      console.log(this.uploadPictures[0]);
      if (!this.$refs.form.validate()) return;
      // console.log(this.uploadPictures);

      let formData = new FormData();
      for (let i = 0; i < this.uploadPictures.length; i++)
        formData.append("file1[]", this.uploadPictures[i]);

      formData.append("tag", this.selectTags);
      formData.append("room_city", this.selectCity);
      formData.append("room_info", this.description);
      formData.append("cost", this.roomCost);
      formData.append("address", this.address);
      formData.append("room_name", this.roomName);
      formData.append("room_latitude", this.lat);
      formData.append("room_longtitude", this.lng);

      console.log({
        tag: this.selectTags,
        room_city: this.selectCity,
        room_info: this.description,
        cost: this.roomCost,
        address: this.address,
        room_name: this.roomName,
        room_latitude: this.lat,
        room_longtitude: this.lng,
      });

      console.log();
      console.log(formData);
      if (this.$cookies.get("alreadyLogin")) {
        this.$axios
          .post("http://localhost:8000/api/room/createRoom.php", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${this.$cookies.get("token")}`,
            },
          })
          .then((res) => {
            console.log(res.data);
            if (res.data.success) {
              console.log("success!");
              // console.log(res.data.message);
              Vue.$toast.open({
                message: "登記成功!",
                type: "success",
                position: "top",
                duration: 2000,
                // all of other options may go here
              });
            } else {
              console.log("登記失敗!");
              let errorMessage = res.data.message;
              if (errorMessage == "invalid token")
                errorMessage = "用戶資訊已失效 請重新登入";
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
            console.error(error.response.data.message);
          });
      } else {
        Vue.$toast.open({
          message: "您必須先登入",
          type: "info",
          position: "top",
          duration: 4000,
          // all of other options may go here
        });
      }

      console.log(this.uploadPictures);
    },
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
      this.lat = lat;
      this.lng = lng;
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
          _this.lat = results["0"]["geometry"]["location"]["lat"]();
          _this.lng = results["0"]["geometry"]["location"]["lng"]();
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
          Vue.$toast.open({
            message: "查無此處",
            type: "error",
            position: "top",
            duration: 4000,
          });
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
  components: {
    // 'Config':'Config'
  },
};
</script>

<style  scoped>
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
</style>