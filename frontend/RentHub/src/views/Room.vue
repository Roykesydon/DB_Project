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
          <div class="text-truncate text-h3 ma-3 ml-0">{{ roomName }}</div>
          <v-hover v-slot="{ hover }">
            <v-card elevation="24" style="z-index=10;" color="transparent">
              <v-tabs v-model="tab" dark background-color="transparent">
                <v-tab :class="tab == 0 ? 'subtitle-1' : 'subtitle-2'">
                  地圖
                </v-tab>
                <v-tab :class="tab == 1 ? 'subtitle-1' : 'subtitle-2'">
                  圖片
                </v-tab>
              </v-tabs>

              <v-tabs-items v-model="tab">
                <v-tab-item class="fill-height">
                  <v-card
                    width="100%"
                    height="290px"
                    class="text-center fill-height"
                  >
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
                    <v-carousel-item v-for="(item_src, i) in src" :key="i">
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
              v-model="address"
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
              v-model="roomCost"
              :rules="[rules.required, rules.cost]"
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
              <v-chip color="primary" :key="index">
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
            v-model="description"
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
                  姓名：{{ name }}<br />
                  電話：{{ phoneNumber }}<br />
                  信箱：{{ email }}<br />
                </div>
                <v-img
                  :src="profileSrc"
                  aspect-ratio="1"
                  height="150px"
                  width="150px"
                  id="profileImg"
                  class="mx-auto my-auto"
                  style="float:right; z-index=-1;position: absolute; top:20px; right:20px;"
                ></v-img>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="secondary" text :href="getProfileRoute()">
                    個人資料
                  </v-btn>
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
          <v-hover v-slot="{ hover }">
            <v-card elevation="24" style="z-index=10;" color="transparent">
              <v-tabs v-model="tab" dark background-color="transparent">
                <v-tab :class="tab == 0 ? 'subtitle-1' : 'subtitle-2'">
                  地圖
                </v-tab>
                <v-tab :class="tab == 1 ? 'subtitle-1' : 'subtitle-2'">
                  圖片
                </v-tab>
              </v-tabs>

              <v-tabs-items v-model="tab">
                <v-tab-item class="fill-height">
                  <v-card
                    width="100%"
                    height="290px"
                    class="text-center fill-height"
                  >
                    <div id="map" class="fill-height"></div>
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
                    <v-carousel-item v-for="(item_src, i) in src" :key="i">
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
              class="mr-5"
              placeholder="地址"
              outlined
              clearable
              id="address"
              :rules="[rules.required, rules.address]"
              :value="address"
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
          <v-form ref="form" v-model="valid" lazy-validation>
            <div class="d-flex">
              <v-text-field
                class=""
                label="房屋名稱"
                placeholder="房屋名稱"
                id="roomName"
                :value="roomName"
                :rules="[rules.required, rules.roomName]"
                outlined
              ></v-text-field>
              <v-text-field
                class="ml-5"
                label="月租"
                id="roomCost"
                :value="roomCost"
                placeholder="月租"
                :rules="[rules.required, rules.cost]"
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
              :value="description"
            ></v-textarea>
          </v-form>
          <div class="d-flex">
            <v-file-input
              label="File input"
              outlined
              dense
              multiple
              v-model="uploadPictures"
              class="my-auto"
              :rules="[rules.files]"
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
            <v-btn
              class="ma-5 my-auto mb-7 mr-0"
              x-large
              color="primary"
              @click="updateRoom"
            >
              更新資訊
            </v-btn>
          </div>
        </v-col>
        <v-col cols="1" class="mx-auto" height="150%">
          <v-divider vertical class="mx-5"></v-divider>
        </v-col>

        <v-col md="5" class="fill-width fill-height">
          <v-card class="pa-10" outlined color="transparent" height="1000px">
            <span class="text-h4 mb-4">同城市的房屋</span>
            <v-virtual-scroll
              :bench="benched"
              :items="items"
              height="620"
              item-height="310"
              class="sc3 pa-5"
            >
              <template v-slot:default="{ item }">
                <v-list-item class="ma-0">
                  <v-list-item-content>
                    <v-card
                      class="ma-0 mx-auto my-0"
                      elevation="20"
                      height="26vh"
                      max-width="400"
                    >
                      <v-carousel
                        height="18vh"
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
                        <v-col cols="7">
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
                        <v-col cols="5" class="my-auto mx-auto text-right">
                          <v-btn
                            class="ma-2 mr-5"
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
import Vue from "vue";
export default {
  name: "RegisterRoom",

  mounted() {
    // // AIzaSyA07oLGCFBea5dZRB179KhJKhrbkUzTzpE
    // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.$config.GOOGLE-MAP-API-KEY )
    // recaptchaScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+ this.googleMapAPI )
    console.log(this.$route.params["id"]);
    let _this = this;
    this.$axios
      .get("http://localhost:8000/api/room/readRoomByRoomID.php", {
        params: { room_ID: this.$route.params["id"] },
      })
      .then((res) => {
        console.log(res.data[0]);
        let data = res.data[0];
        // console.log(data);
        if (data.user_ID == _this.$cookies.get("user_ID")) {
          _this.isRoomOwner = true;
        } else {
          _this.isRoomOwner = false;
        }
        _this.user_ID = data.user_ID;
        _this.roomName = data.room_name;
        _this.roomCost = data.cost;
        _this.description = data.room_info;
        _this.selectTags = data.services;
        _this.selectCity = data.room_city;
        _this.address = data.address;
        _this.lng = parseFloat(data.room_longitude);
        _this.lat = parseFloat(data.room_latitude);

        _this.updateSameCityRoom();
        let tmp = [];
        for (let j = 0; j < data.URLs.length; j++) {
          tmp.push(
            "http://localhost:8000/api/room/getRoomPicture.php?user_ID=" +
              data.user_ID +
              "&URL=" +
              data.URLs[j]
          );
        }
        console.log(data.user_ID);
        _this.profileSrc =
          "http://localhost:8000/api/user/getProfileImage.php?user_ID=" +
          data.user_ID;

        _this.src = tmp;
        this.$axios
          .get("http://localhost:8000/api/user/getProfileByUserID.php", {
            params: { user_ID: data.user_ID },
          })
          .then((res) => {
            console.log(res.data.record);
            _this.email = res.data.record[0]["email"];
            _this.name = res.data.record[0]["user_name"];
            _this.phoneNumber = res.data.record[0]["phone_number"];
          })
          .catch((error) => {
            Vue.$toast.open({
              message: "發生錯誤",
              type: "error",
              position: "top",
              duration: 3000,
              // all of other options may go here
            });
            console.log("network error!");
            console.error(error.response);
          });
      })
      .catch((error) => {
        console.log("network error!");
        console.error(error.response.data.message);
      });

    this.isRoomOwner = false;
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://maps.googleapis.com/maps/api/js?key=" +
        CONFIG.googleMapAPI +
        "&libraries=places"
    );
    document.head.appendChild(recaptchaScript);

    initMap();

    console.log(this.tab);
  },

  data: () => ({
    roomImageContain: false,
    map: null,
    tab: null,
    address: "",
    address: "",
    roomName: "",
    roomCost: 0,
    markers: [],
    roomName: "",
    benched: 20,
    profileSrc: "../assets/blank-profile-picture.png",
    selectTags: [],
    rules: CONFIG.rules,
    isRoomOwner: true,
    description: "",
    dialog: false,
    phoneNumber: "",
    email: "",
    name: "",
    roomCost: 0,
    lat: 0,
    lng: 0,
    src: [],
    uploadPictures: [],
    markers: [],
    valid: true,
    items: [
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
    nightModeStyles: CONFIG.nightModeStyles,
  }),

  methods: {
    getProfileRoute() {
      return "/profile/" + this.user_ID;
    },

    fetchData(fetchIndex, _city) {
      let _this = this;
      console.log({
        index: fetchIndex,
        cost: [0, 20000].join(","),
        city: _city,
      });

      let tmp = {
        index: fetchIndex,
        cost: [0, 20000].join(","),
        city: _city,
      };

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

    updateSameCityRoom() {
      this.$axios
        .get("http://localhost:8000/api/room/getCityRoomCount.php", {
          params: { roomCity: this.selectCity },
        })
        .then((res) => {
          console.log(res.data);
          let count = res.data;
          if (count <= 20) {
            this.fetchData(0, this.selectCity);
          } else {
            this.fetchData(
              Math.floor(Math.random() * parseInt(count / 20)),
              this.selectCity
            );
          }
        })
        .catch((error) => {
          console.log("network error!");
          console.error(error.response);
        });
    },
    deleteRoom() {
      // console.log(userInfo);
      this.$axios
        .post(
          "http://localhost:8000/api/room/deleteRoom.php",
          {
            room_ID: this.$route.params["id"],
          },
          {
            headers: {
              Authorization: `Bearer ${this.$cookies.get("token")}`,
            },
          }
        )
        .then((res) => {
          if (res.data.success) {
            console.log("success!");
            Vue.$toast.open({
              message: "刪除成功!",
              type: "success",
              position: "top",
              duration: 2000,
              // all of other options may go here
            });
            this.signUpOverlay = false;
            let _this = this;
            setTimeout(function () {
              // _this.$router.pop();
              _this.$router.push({
                path: "/profile/" + this.$cookies.get("user_ID"),
              });
            }, 1500);
            // this.$cookies.set("token", "25j_7Sl6xDq2Kc3ym0fmrSSk2xV2XkUkX");
            // this.alreadyLogin = true;
            console.table(res.data);
          } else {
            console.log(res);
            console.log(res.data.message);
          }
        })
        .catch((error) => {
          console.log("network error!");
          console.error(error.response);
          let errorMessage = error.response.data.message;
          if (errorMessage == "invalid token.") {
            errorMessage = "登入過期，請重新登入";
            console.log(app);
            this.$cookies.remove("token");
            this.$cookies.remove("alreadyLogin");
            this.$cookies.remove("user_ID");
            let _this = this;
            setTimeout(function () {
              location.reload();
            }, 1500);
          }

          Vue.$toast.open({
            message: errorMessage,
            type: "error",
            position: "top",
            duration: 3000,
            // all of other options may go here
          });
        });
    },
    updateRoom() {
      if (!this.$refs.form.validate()) return;
      let formData = new FormData();
      // console.log(this.uploadPictures);
      for (let i = 0; i < this.uploadPictures.length; i++)
        formData.append("file1[]", this.uploadPictures[i]);

      formData.append("room_ID", this.$route.params["id"]);
      formData.append("tag", this.selectTags);
      formData.append("room_city", this.selectCity);
      formData.append(
        "room_info",
        document.getElementById("description").value
      );
      formData.append("cost", document.getElementById("roomCost").value);
      formData.append("address", document.getElementById("address").value);
      formData.append("room_name", document.getElementById("roomName").value);
      formData.append("room_latitude", this.lat);
      formData.append("room_longitude", this.lng);

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

      this.$axios
        .post("http://localhost:8000/api/room/updateRoom.php", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${this.$cookies.get("token")}`,
          },
        })
        .then((res) => {
          if (res.data.success) {
            // console.log(res.data);
            Vue.$toast.open({
              message: "更新成功",
              type: "success",
              position: "top",
              duration: 2000,
            });
          } else {
            let errorMessage = res.data.message;
            Vue.$toast.open({
              message: errorMessage,
              type: "error",
              position: "top",
              duration: 4000,
              // all of other options may go here
            });
            console.log("error!");
            console.log(res.data.message);
          }
        })
        .catch((error) => {
          console.log("network error!");
          console.error(error);
          let errorMessage = error.response.data.message;
          if (errorMessage == "invalid token.") {
            errorMessage = "登入過期，請重新登入";
            console.log(app);
            this.$cookies.remove("token");
            this.$cookies.remove("alreadyLogin");
            this.$cookies.remove("user_ID");
            setTimeout(function () {
              location.reload();
            }, 1500);
          }

          Vue.$toast.open({
            message: errorMessage,
            type: "error",
            position: "top",
            duration: 3000,
            // all of other options may go here
          });
          let _this = this;
        });
    },
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
      this.lat = marker.getPosition().lat();
      this.lng = marker.getPosition().lng();
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
        query: document.getElementById("address").value,
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
          _this.lat = results["0"]["geometry"]["location"]["lat"]();
          _this.lng = results["0"]["geometry"]["location"]["lng"]();
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
      let location = {
        lat: this.lat,
        lng: this.lng,
      };
      this.geocoder = new google.maps.Geocoder();

      let renderMap = "map";
      if (!this.isRoomOwner) renderMap = "map2";
      this.map = new google.maps.Map(document.getElementById(renderMap), {
        center: location,
        zoom: 16,
        mapTypeId: "roadmap",
        disableDefaultUI: true,
      });

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
      console.log(this.tab);
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