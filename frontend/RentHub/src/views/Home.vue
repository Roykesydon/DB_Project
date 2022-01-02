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
          ><v-overlay
            :value="overlay"
            :opacity="0.7"
            class="align-start-center"
          ></v-overlay
        ></v-img>
      </v-carousel-item>
    </v-carousel>
    <div id="floatDiv">
      <div class="text-h1 font-weight-black">RentHub</div>
      <div class="text-h4 font-weight-thin">也許不是台灣最好的租屋網</div>
      <v-btn
        class="ma-2"
        :loading="loading"
        :disabled="loading"
        color="primary"
        @click="insertTestData()"
      >
        新增測試資料
      </v-btn>
    </div>
  </v-card>
</template>

<script>
export default {
  name: "Home",
  data: () => ({
    showTitle: false,
    overlay: true,
    indexBackground: [
      "olexandr-ignatov-w72a24brINI-unsplash.jpg",
      "chastity-cortijo-M8iGdeTSOkg-unsplash.jpg",
      "FBXuXp57eM0.jpg",
      "ralph-ravi-kayden-FqqiAvJejto-unsplash.jpg",
    ],
    loading: false,
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
  }),

  methods: {
    timeout(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },

    async sleep(ms) {
      await this.timeout(ms);
      return 1;
    },

    async register(userInfo) {
      console.log(userInfo);
      await this.$axios.post(
        "http://localhost:8000/api/register.php",
        userInfo
      );
      return true;
    },

    async login(info) {
      let userInfo = {
        password: info.password,
        user_ID: info.id,
      };
      await this.$axios
        .post("http://localhost:8000/api/login.php", userInfo)
        .then((res) => {
          console.log(res);
          info.token = res.data.token;
        });
    },

    getRandomSubarray(arr, size) {
      var shuffled = arr.slice(0),
        i = arr.length,
        temp,
        index;
      while (i--) {
        index = Math.floor((i + 1) * Math.random());
        temp = shuffled[index];
        shuffled[index] = shuffled[i];
        shuffled[i] = temp;
      }
      return shuffled.slice(0, size);
    },

    async registerRoom(token) {
      let randomSentence = require("random-sentence");
      let addressList = [
        "220台灣新北市板橋區光正街9巷",
        "110台灣台北市信義區臺北市信義區西村里8鄰信義路五段",
        "330台灣桃園市桃園區縣府路",
        "403台灣台中市西區五權路",
        "701台灣台南市東區小東路198巷6弄",
        "801台灣高雄市前金區中山一路",
        "302台灣新竹縣竹北市中山路232巷5弄",
        "360台灣苗栗縣苗栗市中山路",
        "504台灣彰化縣秀水鄉彰秀路",
        "545台灣南投縣埔里鎮中正路",
        "632台灣雲林縣虎尾鎮德興路",
        "613台灣嘉義縣朴子市",
        "920台灣屏東縣潮州鎮愛國路",
        "266台灣宜蘭縣三星鄉尾塹路",
        "970台灣花蓮縣花蓮市國興六街",
        "954台灣台東縣卑南鄉泰安村",
        "880台灣澎湖縣馬公市六合路",
        "環島南路四段",
        "連江縣介壽路89",
        "200台灣基隆市仁愛區自來街13巷",
        "300台灣新竹市北區南清公路",
        "600台灣嘉義市西區中山路",
      ];
      let geoList = [
        [25.016796, 121.4627757],
        [25.0327844, 121.5608822],
        [24.9925084, 121.3005067],
        [24.1446756, 120.6706484],
        [22.9996172, 120.2269723],
        [22.6300024, 120.3017892],
        [24.8374528, 121.0180469],
        [24.5589951, 120.8203119],
        [24.0419829, 120.5167615],
        [23.9654792, 120.9669907],
        [23.7100991, 120.4310717],
        [23.4464152, 120.2570421],
        [22.5516989, 120.5467839],
        [24.6981821, 121.7425971],
        [23.9873749, 121.5989503],
        [22.8161965, 121.0507098],
        [23.5745362, 119.5809023],
        [24.43803, 118.400997],
        [26.157193, 119.9524916],
        [25.1275946, 121.7393513],
        [24.8232069, 120.9582752],
        [23.4796145, 120.4462277],
      ];

      for (let cityIndex = 0; cityIndex < addressList.length; cityIndex++) {
        let formData = new FormData();

        let images = [];
        for (let j = 0; j < Math.floor(Math.random() * 4 + 1); j++) {
          let imageUrl =
            "https://picsum.photos/1920/1080?" +
            cityIndex * 4 +
            j +
            String(Date.now());

          await fetch(imageUrl)
            .then((response) => response.blob())
            .then((imageBlob) => {
              console.log(imageBlob);
              // imageBlob.lastModifiedDate = new Date();
              // imageBlob.name = this.makeRandomString(10)+".jpg";
              imageBlob = new File([imageBlob], this.makeRandomString(10)+".jpg");
              console.log(imageBlob);
              images.push(imageBlob);
            });
        }

        for (let i = 0; i < images.length; i++)
          formData.append("file1[]", images[i]);

        formData.append(
          "tag",
          this.getRandomSubarray(
            this.roomTags,
            Math.floor(Math.random() * this.roomTags.length)
          )
        );
        formData.append("room_city", this.cities[cityIndex]);
        formData.append("room_info", randomSentence({ min: 15, max: 20 }));
        formData.append(
          "cost",
          Math.floor((Math.random() * (9000 - 3000) + 3000) / 100) * 100
        );
        formData.append("address", addressList[cityIndex]);
        formData.append("room_name", randomSentence({ min: 1, max: 3 }));
        formData.append("room_latitude", geoList[cityIndex][0]);
        formData.append("room_longtitude", geoList[cityIndex][1]);
        console.log({
          room_city: this.cities[cityIndex],
          room_info: randomSentence({ min: 15, max: 20 }),
          cost: Math.floor((Math.random() * (9000 - 3000) + 3000) / 100) * 100,
          address: addressList[cityIndex],
          room_name: randomSentence({ min: 3, max: 5 }),
          room_latitude: geoList[cityIndex][0],
          room_longtitude: geoList[cityIndex][1],
        });
        await this.$axios
          .post("http://localhost:8000/api/room/createRoom.php", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
          })
          .then((res) => {
            // console.log(res.data);
            if (res.data.success) {
            } else {
              console.log(res.data.message);
              console.log("create error");
            }
          })
          .catch((error) => {
            console.log("network error!");
            console.error(error.response.data.message);
          });
      }
    },

    insertErrorToast() {
      Vue.$toast.open({
        message: "生成資料過程中發生錯誤",
        type: "error",
        position: "top",
        duration: 4000,
        // all of other options may go here
      });
    },

    makeRandomString(length, format = null) {
      var result = "";
      var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      if (format == "number") characters = "0123456789";
      var charactersLength = characters.length;
      for (var i = 0; i < length; i++) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
      }
      return result;
    },

    async insertTestData() {
      this.loading = true;
      let promise = [];
      let randomSentence = require("random-sentence");
      // console.log(await this.sleep(3000));
      // console.log("ok");
      let users = [];
      for (let i = 0; i < 5; i++) {
        let userInfo = {
          name: randomSentence({ words: 1 }),
          id: this.makeRandomString(25),
          password: "123456789",
          email: this.makeRandomString(15) + "@gmail.com",
          phoneNumber: "09" + this.makeRandomString(8, "number"),
          token: "",
        };
        users.push(userInfo);
      }
      console.log(users);
      for (let i = 0; i < users.length; i++) {
        // console.log(i);
        await this.register(users[i]);
      }
      for (let i = 0; i < users.length; i++) {
        // console.log(i);
        await this.login(users[i]);
        console.log(users[i].token);
        if (users[i].token == "") console.log("login error");
      }

      for (let i = 0; i < users.length; i++) {
        console.log(users[i].token);
        await this.registerRoom(users[i].token);
      }
      this.loading = false;
    },
    mounted() {
      // this.sleep(2000).then((response) => {
      //   this.showTitle = true;
      // });
    },
  },
};
</script>

<style scope>
#floatDiv {
  position: absolute;
  top: 300px;
  left: 150px;
}
</style>