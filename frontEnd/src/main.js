// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from './store/'
import router from './router'
import auth from './lib/auth'

//指定Vue使用扩展
Vue.use(VueAxios, axios)

//路由中间件
router.beforeEach((to, from, next) => {
    //初始化
    store.state.msg = false
    //验证是否需要登录
    if (to.matched.some(record => { return record.meta.requiresAuth })) {
        //验证是否有token
        if(store.state.token === ''){
            if (!auth.loggedIn()){
                //跳转至登录
                next({ path: '/login' })
                return
            }
            store.state.token = auth.getToken()
            store.state.user = auth.getUser()
        }
    }
    next()
})
//路由中间件
router.afterEach(route => {
    document.title = route.meta.title
})

/* eslint-disable no-new */
new Vue({
  store,
  el: '#app',
  template: '<App/>',
  components: { App },
  router
})
