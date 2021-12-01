<template>
  <v-container class ="fill-height">
      <v-row class="text-center fill-height" height="100%" width="85%">
        <v-col md="8">
          <v-card width="100%" class="text-center fill-height">
            <div id="map" ></div>
          </v-card>
        </v-col>
        <v-col md="4" class="fill-width">
          <!-- <v-card
            class="mx-auto my-12"
            max-width="374"
          >
            <v-img
              height="250"
              src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
            ></v-img>

            <v-card-title>Cafe Badilico</v-card-title>

            <v-card-text>
              <v-row
                align="center"
                class="mx-0"
              >
                <v-rating
                  :value="4.5"
                  color="amber"
                  dense
                  half-increments
                  readonly
                  size="14"
                ></v-rating>

                <div class="grey--text ms-4">
                  4.5 (413)
                </div>
              </v-row>

              <div class="my-4 text-subtitle-1">
                $ • Italian, Cafe
              </div>

              <div>Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.</div>
            </v-card-text>

            <v-divider class="mx-4"></v-divider>

            <v-card-title>Tonight's availability</v-card-title>

            <v-card-text>
              <v-chip-group
                active-class="deep-purple accent-4 white--text"
                column
              >
                <v-chip>5:30PM</v-chip>

                <v-chip>7:30PM</v-chip>

                <v-chip>8:00PM</v-chip>

                <v-chip>9:00PM</v-chip>
              </v-chip-group>
            </v-card-text>
            <v-text-field label="Another input" class="ma-4"></v-text-field>
            <v-card-actions>
              <v-btn
                color="deep-purple lighten-2"
                text
                @click="geoCoding()"
              >
                Reserve
              </v-btn>
            </v-card-actions>
          </v-card> -->
          <v-card height="200px">
            <v-toolbar card>
              <v-toolbar-title>Title</v-toolbar-title>
            </v-toolbar>

            <v-divider></v-divider>

            <v-card-text>
              <v-list>
                <template v-for="i in 40">
                  <v-list-tile>
                    <v-list-tile-content>
                      <div>{{ i }}</div>
                    </v-list-tile-content>
                  </v-list-tile>
                </template>
              </v-list>
            </v-card-text>

            <v-footer class="pa-2">Footer</v-footer>
          </v-card>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
  import * as CONFIG from '../../public/config'
  export default {
    name: 'Map',

    mounted() {
      let recaptchaScript = document.createElement('script')
      // // AIzaSyA07oLGCFBea5dZRB179KhJKhrbkUzTzpE
      // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.$config.GOOGLE-MAP-API-KEY )
      // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.googleMapAPI )
      recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ CONFIG.googleMapAPI + "&libraries=places" )
      document.head.appendChild(recaptchaScript)
      // initMap();
    },

    data: () => ({
      ecosystem: [
        {
          text: 'vuetify-loader',
          href: 'https://github.com/vuetifyjs/vuetify-loader',
        },
        {
          text: 'github',
          href: 'https://github.com/vuetifyjs/vuetify',
        },
        {
          text: 'awesome-vuetify',
          href: 'https://github.com/vuetifyjs/awesome-vuetify',
        },
      ],
    }),

    methods: {
      initMap() {

        // 預設顯示的地點：台北市政府親子劇場
        let location = {
          lat: 25.0374865, // 經度
          lng: 121.5647688 // 緯度
        };

        // 建立地圖
        this.map = new google.maps.Map(document.getElementById('map'), {
          center: location, // 中心點座標
          zoom: 16, // 1-20，數字愈大，地圖愈細：1是世界地圖，20就會到街道
          /*
            roadmap 顯示默認道路地圖視圖。
            satellite 顯示 Google 地球衛星圖像。
            hybrid 顯示正常和衛星視圖的混合。
            terrain 顯示基於地形信息的物理地圖。
          */
          mapTypeId: 'terrain'
        });

      // 放置marker
        let marker = new google.maps.Marker({
          position: location,
          map: this.map
        });


      },
      geoCoding() {


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
          query: '海大會館',
          fields: ['name', 'geometry'],
        };
        var service = new google.maps.places.PlacesService(this.map);
        service.findPlaceFromQuery(request, function(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            console.log(results)
            // for (var i = 0; i < results.length; i++) {
            //   createMarker(results[i]);
            // }
            // map.setCenter(results[0].geometry.location);
          }
        });
      },
      test(){

        let location = {
          lat: 25.1374865, // 經度
          lng: 121.5647688 // 緯度
        };
        let marker = new google.maps.Marker({
          position: location,
          map: this.map
        });
      }
    },
    created: function() {
      window.addEventListener('load', () => {
        this.initMap();
      });
    }
  }

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