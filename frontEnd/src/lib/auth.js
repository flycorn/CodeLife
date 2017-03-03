/**
 * Author: flycorn
 * Email: ym1992it@163.com
 * Date: 2017/2/6
 * Time: 11:04
 *
 * 用户数据
 */
export default {

    setToken (token){
        localStorage.setItem('token', token)
    },

    getToken () {
        return localStorage.getItem('token')
    },

    setUser (user) {
        localStorage.setItem('user', JSON.stringify(user))
    },

    getUser () {
        return localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
    },

    loggedIn () {
        return !!localStorage.getItem('token')
    },

    logout () {
        localStorage.removeItem('user');
        localStorage.removeItem('token');
    }

}