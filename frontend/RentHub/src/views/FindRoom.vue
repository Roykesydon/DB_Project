<template>
  <div>
    <div class="my-15 mx-auto px-10 py-5" style="max-width: 1300px">
      <div class="mx-10 pa-2 my-0 pb-0">
        <v-container>
          <div class="text-h5 mb-1">在此搜尋</div>
          <div class="d-flex mb-0">
            <v-text-field
              class="mr-10"
              placeholder="關鍵字"
              outlined
              clearable
            ></v-text-field>
            <v-btn dark x-large color="primary"> 搜尋 </v-btn>
          </div>
            <v-select
              :items="roomTags"
              item-text="text"
              attach
              chips
              label="tag"
              multiple
              outlined
            ></v-select>
        </v-container>
      </div>
      <div class="ma-0 pa-5">
        <v-container>
          <v-row dense>
            <v-col v-for="(item, i) in items" :key="i" cols="12">
              <v-divider v-if="i != 0" class="ma-3 mb-5"></v-divider>
              <v-row>
                <v-col cols="8">
                  <span>
                    <div class="text-h5 pa-10 pb-0" v-text="item.title"></div>
                    <div class="px-10 pt-2 pb-5">
                      <div v-text="item.address"></div>
                      <div>人數: {{ item.capacity }}</div>
                      <div>坪數: {{ item.squareMeters }}</div>

                      <v-btn class="mt-5" outlined rounded small>
                        更多詳情
                      </v-btn>
                    </div>
                  </span>
                </v-col>
                <v-col cols="4" class="my-auto">
                  <span class="my-auto">
                    <v-img
                      :src="item.src"
                      aspect-ratio="1.0"
                      max-width="200"
                      class="my-auto"
                    ></v-img>
                  </span>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-container>
        <div class="text-center">
          <v-pagination
            v-model="page"
            :length="15"
            :total-visible="7"
            color="primary"
            :light="true"
          ></v-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    page: 1,
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
    items: [
      {
        title: "台北101",
        src: "https://attach.setn.com/newsimages/2020/11/08/2869857-PH.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        title: "台北101",
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
      {
        src: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
        address: "台北市信義區信義路五段7號",
        cost: 18000,
        capacity: 3,
        squareMeters: 11.0,
      },
    ],
  }),
};
</script>