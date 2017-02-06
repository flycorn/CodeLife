<template>
    <div class="todo">
        <router-link to="/" class="btn btn-default">返回</router-link>
        <div v-if="error" class="error">
            {{ error }}
        </div>

        <div v-if="todo" class="content">
            <h2>{{ todo.title }}</h2>
        </div>
    </div>
</template>
<style>
    .error{
        color: red;
    }
</style>
<script>
    export default{
      created () {
        // 组件创建完后获取数据，
        // 此时 data 已经被 observed 了
        this.fetchData()
      },
      watch: {
        // 如果路由有变化，会再次执行该方法
        '$route': 'fetchData'
      },
      methods: {
        fetchData () {
            this.$store.dispatch('getTodo', this.$route.params.id)
        }
      },
      computed: {
        todo() {
            return this.$store.state.todo
        },
        error() {
            return this.$store.state.error
        }
      }
    }
</script>
