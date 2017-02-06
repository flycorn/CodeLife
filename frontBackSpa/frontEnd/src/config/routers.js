/**
 * Author: flycorn
 * Email: ym1992it@163.com
 * Date: 2017/2/6
 * Time: 11:04
 *
 * 应用路由配置
 */
import Todos from '../components/Todos'
import Todo from '../components/Todo'
import Login from '../components/Login'
import Register from '../components/Register'

//定义路由
export default [
    { path: '/register', name: 'register', component: Register, meta: { title: '注册' } },
    { path: '/login', name: 'login', component: Login, meta: { title: '登录' } },
    { path: '/', name: 'index', component: Todos, meta: { title: '我的备忘录', requiresAuth: true } },
    { path: '/todo/:id', name: 'todo', component: Todo, meta: { title: '备忘录', requiresAuth: true } },
    { path: '*', redirect: '/' }
];
