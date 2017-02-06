<template>
    <div class="register">
        <form v-on:submit.prevent="register(registerForm)">
            <div class="form-group">
                <label for="name">昵称：</label>
                <div class="input-group">
                    <span class="input-group-addon">N</span>
                    <input type="text" class="form-control" id="name" v-model="registerForm.name" name="name" placeholder="请输入昵称">
                </div>
            </div>
            <div class="form-group">
                <label for="email">邮箱：</label>
                <div class="input-group">
                    <span class="input-group-addon">E</span>
                    <input type="email" class="form-control" id="email" v-model="registerForm.email" name="email" placeholder="请输入邮箱">
                </div>
            </div>
            <div class="form-group">
                <label for="password">密码：</label>
                <div class="input-group">
                    <span class="input-group-addon">P</span>
                    <input type="password" class="form-control" id="password" v-model="registerForm.password" name="password" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-primary">注册</button>
                <router-link to="login" class="btn btn-default">登录</router-link>
            </div>
        </form>
    </div>
</template>
<style>

</style>
<script>
    export default{
        name: 'register',
        data() {
            return {
                registerForm: {
                    name: '',
                    email: '',
                    password: ''
                }
            }
        },
        methods: {
          register(form) {
               this.$store.dispatch('register', form)
          }
        }
    }
</script>