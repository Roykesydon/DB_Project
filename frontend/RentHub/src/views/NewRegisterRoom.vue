<template>
  <v-card
    class="my-15 mx-auto px-10 py-5"
    max-width="70%"
    height="85%"
    color="transparent"
    outlined
  >
    <v-stepper v-model="currentStep">
      <v-stepper-header>
        <v-stepper-step :complete="currentStep > 1" step="1">
          輸入地址
        </v-stepper-step>

        <v-divider></v-divider>

        <v-stepper-step :complete="currentStep > 2" step="2">
          Name of step 2
        </v-stepper-step>

        <v-divider></v-divider>

        <v-stepper-step step="3"> Name of step 3 </v-stepper-step>
      </v-stepper-header>

      <v-stepper-items>
        <v-stepper-content step="1">
          <v-row class="fill-height">
            <v-col cols="5">
              <v-card
                width="100%"
                height="300"
                class="text-center fill-height"
                color="transparent"
                outlined
              >
                <div id="map" style="height: 100%"></div>
              </v-card>
            </v-col>
            <v-col cols="1" class="mx-auto" height="auto">
              <v-divider vertical class="mx-5"></v-divider>
            </v-col>
            <v-col cols="5">
              <div class="d-flex mb-0">
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
            </v-col>
          </v-row>

          <div class="text-h5 mb-2 mt-5">輸入地址</div>

          <v-btn color="primary" @click="currentStep = 2"> Continue </v-btn>

          <v-btn text> Cancel </v-btn>
        </v-stepper-content>

        <v-stepper-content step="2">
          <v-card class="mb-12" color="grey lighten-1" height="200px"></v-card>

          <v-btn color="primary" @click="currentStep = 3"> Continue </v-btn>

          <v-btn text> Cancel </v-btn>
        </v-stepper-content>

        <v-stepper-content step="3">
          <v-card class="mb-12" color="grey lighten-1" height="200px"></v-card>

          <v-btn color="primary" @click="currentStep = 1"> Continue </v-btn>

          <v-btn text> Cancel </v-btn>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
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
    currentStep: 1,
    map: null,
    address: "",
    markers: [],
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
</style>