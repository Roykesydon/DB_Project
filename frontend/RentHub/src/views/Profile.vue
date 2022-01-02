<template>
  <v-card class="my-15 mx-auto px-10 py-5" max-width="70%" height="85%">
    <v-container class="fill-height">
      <v-row class="fill-height" height="100%" width="50%">
        <v-col md="5">
          <div style="height: 100%">
            <v-row>
              <v-col cols="12">
                <v-img
                  :src="getUserPofilePictureUrl()"
                  aspect-ratio="1"
                  height="250px"
                  width="250px"
                  id="profileImg"
                  class="mx-auto my-auto"
                ></v-img>
              </v-col>
            </v-row>
            <div
              class="text-h4 my-3"
              style="text-align: center"
              v-if="!isSelfProfile"
            >
              {{ name }}
            </div>
            <v-row class="mx-auto">
              <v-col class="mx-auto" cols="7">
                <v-text-field
                  class="ma-3 my-auto centered-input text-h4"
                  v-model="name"
                  :rules="[rules.required, rules.name]"
                  v-if="isSelfProfile"
                  hide-details="auto"
                  max-width="20px"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-divider class="my-7"></v-divider>
            <div>
              <v-row>
                <v-col cols="3" class="my-auto align-end">
                  <div class="text-h5 text-lg-right">電話：</div>
                </v-col>
                <v-col cols="9" v-if="!isSelfProfile">
                  <v-text-field
                    class="ma-3 my-auto"
                    v-model="phone_number"
                    counter
                    maxlength="25"
                    hide-details="auto"
                    readonly
                  ></v-text-field>
                </v-col>
                <v-col cols="9" v-if="isSelfProfile">
                  <v-text-field
                    class="ma-3 my-auto"
                    v-model="phone_number"
                    counter
                    :rules="[rules.required, rules.phoneNumber]"
                    maxlength="25"
                    hide-details="auto"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="3" class="my-auto">
                  <div class="text-h5 text-lg-right">信箱：</div>
                </v-col>
                <v-col cols="9" v-if="!isSelfProfile">
                  <v-text-field
                    class="ma-3 my-auto"
                    v-model="email"
                    counter
                    readonly
                    maxlength="50"
                    hide-details="auto"
                  ></v-text-field
                ></v-col>
                <v-col cols="9" v-if="isSelfProfile">
                  <v-text-field
                    class="ma-3 my-auto"
                    v-model="email"
                    counter
                    :rules="[rules.required, rules.email]"
                    maxlength="50"
                    hide-details="auto"
                  ></v-text-field
                ></v-col>
              </v-row>
              <v-row v-if="isSelfProfile">
                <v-col cols="3">
                  <div class="text-h5 text-lg-right">頭像：</div>
                </v-col>
                <v-col cols="9">
                  <v-file-input
                    label="上傳照片"
                    outlined
                    dense
                    class="my-auto"
                    accept="image/png, image/jpeg, image/bmp"
                    v-model="uploadPicture"
                  ></v-file-input
                ></v-col>
              </v-row>
            </div>
            <div
              class="d-flex align-end"
              style="height: 80px"
              v-if="isSelfProfile"
            >
              <v-spacer></v-spacer>
              <v-btn
                color="primary"
                x-large
                dark
                style="float: right"
                @click="updateProfile()"
              >
                更新資料
              </v-btn>
            </div>
          </div>
        </v-col>
        <v-col md="7" class="fill-width">
          <v-card class="pa-10" outlined color="transparent">
            <span class="text-h4 mb-4">出租房屋</span>
            <v-virtual-scroll
              :bench="benched"
              :items="items"
              height="620"
              item-height="400"
              class="sc3 pa-5"
            >
              <template v-slot:default="{ item }">
                <v-list-item>
                  <v-list-item-content>
                    <v-card
                      class="ma-10 mx-auto my-0"
                      elevation="20"
                      height="350px"
                      max-width="500"
                    >
                      <v-carousel
                        height="250"
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
import Vue from "vue";
import { Carousel, Slide } from "vue-carousel";
import * as CONFIG from "../../public/config";
import app from "../App";
export default {
  name: "Profile",

  components: {
    Carousel,
    Slide,
  },

  mounted() {
    console.log(this.$route.params["id"], this.$cookies.get("user_ID"));
    if (this.$route.params["id"] == this.$cookies.get("user_ID"))
      this.isSelfProfile = true;
    else this.isSelfProfile = false;
    this.getUserData();
  },

  data: () => ({
    isSelfProfile: false,
    profileImage: "../assets/blank-profile-picture.png",
    name: "無資料",
    rules: CONFIG.rules,
    phone_number: "無資料",
    email: "無資料",
    benched: 20,
    uploadPicture: null,
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
  }),
  methods: {
    getRoomRoute(id) {
      return "/room/" + String(id);
    },

    getUserPofilePictureUrl() {
      return (
        "http://localhost:8000/api/user/getProfileImage.php?user_ID=" +
        this.$route.params["id"]
      );
    },

    getUserData() {
      let _this = this;
      this.$axios
        .get("http://localhost:8000/api/user/getProfileByUserID.php", {
          params: { user_ID: this.$route.params["id"] },
        })
        .then((res) => {
          console.log(res.data.record);
          _this.email = res.data.record[0]["email"];
          _this.name = res.data.record[0]["user_name"];
          _this.phone_number = res.data.record[0]["phone_number"];
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
      this.$axios
        .get("http://localhost:8000/api/room/readRoomByUserID.php", {
          params: { user_ID: this.$route.params["id"] },
        })
        .then((res) => {
          console.log(res.data.records);
          this.items = [];
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
              console.log(
                "http://localhost:8000/api/room/getRoomPicture.php?user_ID=" +
                  this.$route.params["id"] +
                  "&URL=" +
                  data[i].URLs[j]
              );
              tmp.src.push(
                "http://localhost:8000/api/room/getRoomPicture.php?user_ID=" +
                  this.$route.params["id"] +
                  "&URL=" +
                  data[i].URLs[j]
              );
            }
            this.items.push(tmp);
          }
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
          console.error(error);
        });
    },

    updateProfile() {
      console.log(this.$cookies.get("token"));
      console.log(`Bearer ${this.$cookies.get("token")}`);
      let formData = new FormData();
      formData.append("file", this.uploadPicture);
      formData.append("user_name", this.name);
      formData.append("email", this.email);
      formData.append("phoneNumber", this.phone_number);
      this.$axios
        .post("http://localhost:8000/api/user/updateProfile.php", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${this.$cookies.get("token")}`,
          },
        })
        .then((res) => {
          if (res.data.success) {
            console.log(res.data);
            Vue.$toast.open({
              message: "上傳成功",
              type: "success",
              position: "top",
              duration: 2000,
            });
          } else {
            let errorMessage = res.data.message;
            if (errorMessage == "Invalid Email Address!")
              errorMessage = "信箱不存在";
            else if (errorMessage == "Invalid Password!")
              errorMessage = "密碼錯誤";
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
          // Vue.$toast.open({
          //   message: "發生錯誤",
          //   type: "error",
          //   position: "top",
          //   duration: 3000,
          //   // all of other options may go here
          // });
          console.log("network error!");
          console.error(error.response.data);
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
        });
    },
  },
  created: function () {},
};
</script>

<style  scoped>
#profileImg {
  border-radius: 50%;
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

.centered-input >>> input {
  text-align: center;
}
/* .VueCarousel-pagination{
  float:left;
  position: absolute;
} */

/* .VueCarousel-dot{
  float:left;
  position: absolute;
} */
</style>