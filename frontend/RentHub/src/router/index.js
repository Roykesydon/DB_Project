import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import About from '../views/About.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  },
  {
    path: '/test',
    name: 'Test',
    component: () => import( '../views/Test2.vue')
  },
  {
    path: '/findRoom',
    name: 'FindRoom',
    component: () => import( '../views/FindRoom.vue')
  },
  {
    path: '/registerRoom',
    name: 'registerRoom',
    component: () => import( '../views/RegisterRoom.vue')
  },
  {
    path: '/profile/:id',
    name: 'profile',
    component: () => import( '../views/Profile.vue')
  },
  {
    path: '/room/:id',
    name: 'room',
    component: () => import( '../views/Room.vue')
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
