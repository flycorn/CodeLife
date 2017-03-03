<template>
    <div class="login">
        <form v-on:submit.prevent="login(loginForm)">
            <div class="form-group">
                <label for="email">邮箱：</label>
                <div class="input-group">
                    <span class="input-group-addon">E</span>
                    <input type="email" class="form-control" id="email" v-model="loginForm.email" name="email" placeholder="请输入邮箱">
                </div>
            </div>
            <div class="form-group">
                <label for="password">密码：</label>
                <div class="input-group">
                    <span class="input-group-addon">P</span>
                    <input type="password" class="form-control" id="password" v-model="loginForm.password" name="password" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-primary">登录</button>
                <router-link to="register" class="btn btn-default">注册</router-link>
            </div>
        </form>
    </div>
</template>
<style>

</style>
<script>
    export default{
        name: 'login',
        data() {
            return {
                loginForm: {
                    email: '',
                    password: ''
                }
            }
        },
        methods: {
          login(form) {
               this.$store.dispatch('login', form)
          }
        }
    }
</script>