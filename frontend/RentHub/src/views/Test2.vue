<template>
  <div id="vue-instance" class="container">
    <div class="row">
      <div class="col-sm-6" v-for="(post, index) in posts" :key="index">
        <div class="card m-4" style="width: 18rem">
          <img class="card-img-top" src="https://via.placeholder.com/350x150" />
          <div class="card-body">
            <h5 class="card-title">
              <strong>{{ post.title }}</strong>
            </h5>
          </div>
        </div>
      </div>
    </div>
    <mugen-scroll :handler="getPosts" :should-handle="!loading">
      loading...
    </mugen-scroll>
  </div>
</template>

<script>
import MugenScroll from "vue-mugen-scroll";
export default {
  data() {
    return {
      posts: [],
      loading: false,
    };
  },
  created() {
    this.getPosts();
  },
  methods: {
    getPosts() {
      for (var i = 0; i < 16; i++) {
        var count = this.posts.length + i;
        this.posts.push({
          title: "title " + count,
        });
      }
    },
  },
  components: { MugenScroll },
};
</script>