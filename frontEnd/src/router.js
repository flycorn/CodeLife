/**
 * Author: flycorn
 * Email: ym1992it@163.com
 * Date: 2017/2/6
 * Time: 11:04
 *
 * 实例化路由
 */
import routers from './config/routers'
import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

//实例化路由
export default new VueRouter({
    // hashbang: false,
    // history: true,
    routes: routers
})