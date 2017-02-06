/**
 * Author: flycorn
 * Email: ym1992it@163.com
 * Date: 2017/2/6
 * Time: 11:04
 *
 * 应用状态管理
 */

import Vue from 'vue'
import Vuex from 'vuex'
import router from '../router'
import auth from '../lib/auth'

Vue.use(Vuex)

//定义接口域名
const apiUrl = 'http://devapi.flycorn.com/api'

//定义数据
export default new Vuex.Store({
    //数据
    state: {
        loading: false,
        error: false,
        msg: false,
        msgType: 'alert-warning',
        msgText: '我的备忘录....',
        token: '',
        user: { id: null, name: '', email: '' },
        todos: [],
        todo: false,
        newTodo: { id: null, title: '', completed: false }
    },
    mutations: {
        set_token (state, token) {
            state.token = token
        },
        set_user (state, user) {
            state.user = user
        },
        get_todos_list (state, todos) {
            state.todos = todos
        },
        get_todo (state, todo) {
            state.todo = todo
        },
        complete_todo (state, todo) {
            todo.completed = ! todo.completed
        },
        delete_todo (state, index) {
            state.todos.splice(index, 1)
        },
        add_todo (state, todo) {
            state.todos.push(todo)
        },
        show_msg (state, text, type = 'alert-warning'){
            state.msgText = text
            state.msgType = type
            state.msg = true
        },
        close_msg (state){
            state.msgText = ""
            state.msg = false
        }
    },
    //接口
    actions: {
        showMsg (store, msg) {
            store.commit('show_msg', msg)
        },
        //注册
        register(store, form) {
            store.state.loading = true
            Vue.axios.post(apiUrl+'/user/register', form).then(response => {
                store.state.loading = false
                if(response.data.status == 'failed'){
                    store.commit('show_msg', response.data.errors.message)
                    return
                }
                //跳转至登录
                router.push('/login')
            })
        },
        //登录
        login(store, form) {
            store.state.loading = true
            Vue.axios.post(apiUrl+'/user/login', form).then(response => {
                store.state.loading = false
                if(response.data.status == 'failed'){
                    store.commit('show_msg', response.data.errors.message)
                    return
                }
                //保存用户数据
                auth.setToken(response.data.correct.data.token)
                auth.setUser(response.data.correct.data.user)

                //更新vue数据
                store.commit('set_user', response.data.correct.data.user)
                store.commit('set_token', response.data.correct.data.token)

                //跳转至首页
                router.push('/')
            })
        },
        //退出
        logout(store) {
            store.state.todos = []
            store.state.newTodo = { id: null, title: '', completed: false }
            store.commit('set_user', { id: null, name: '', email: '' })
            store.commit('set_token', '')

            auth.logout()

            //跳转至登录
            router.push('/login')
        },
        //获取数据列表
        getTodos(store) {
            if(store.state.token){
                store.state.loading = true
                Vue.axios.post(apiUrl+'/todo', {token: store.state.token}).then(response => {
                    store.state.loading = false
                    if(response.data.status == 'failed'){
                        store.commit('show_msg', response.data.errors.message)
                        return
                    }
                    store.commit('get_todos_list', response.data.correct.data.todos)
                })
            }
        },
        //获取数据详情
        getTodo (store, id) {
            if(store.state.token){
                store.state.todo = false
                store.state.loading = true
                Vue.axios.post(apiUrl+'/todo/' + id, {token: store.state.token}).then(response => {
                    store.state.loading = false
                    if(response.data.status == 'failed'){
                        store.commit('show_msg', response.data.errors.message)
                        return
                    }
                    store.commit('get_todo', response.data.correct.data.todo)
                })
            }
        },
        //标记数据
        completeTodo (store, todo) {
            if(store.state.token){
                store.state.loading = true
                Vue.axios.patch(apiUrl+'/todo/'+todo.id+'/completed', {token: store.state.token}).then(response => {
                    store.state.loading = false
                    if(response.data.status == 'failed'){
                        store.commit('show_msg', response.data.errors.message)
                        return
                    }
                    store.commit('complete_todo', todo)
                })
            }
        },
        //删除数据
        removeTodo (store, payload) {
            if(store.state.token){
                store.state.loading = true
                Vue.axios.post(apiUrl+'/todo/'+payload.todo.id+'/delete', {token: store.state.token}).then(response => {
                    store.state.loading = false
                    if(response.data.status == 'failed'){
                        store.commit('show_msg', response.data.errors.message)
                        return
                    }
                    store.commit('delete_todo', payload.index)
                })
            }
        },
        //保存数据
        saveTodo (store, todo){
            if(store.state.token) {
                store.state.loading = true
                todo.token = store.state.token
                Vue.axios.post(apiUrl + '/todo/create', todo).then(response => {
                    store.state.loading = false
                    if(response.data.status == 'failed'){
                        store.commit('show_msg', response.data.errors.message)
                        return
                    }
                    store.commit('close_msg')
                    store.commit('add_todo', response.data.correct.data.todo)
                })
                store.state.newTodo = {id: null, title: "", completed: false}
            }
        }
    }
})