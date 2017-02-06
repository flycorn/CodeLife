<template>
  <form v-on:submit.prevent="addTodo(newTodo)">
    <div class="form-group">
      <input type="text"
             v-model="newTodo.title"
             class="form-control"
             placeholder="添加一条新的记录"
      />
    </div>
    <div class="from-group">
      <button class="btn btn-success" type="submit">添加记录</button>
    </div>
  </form>
</template>

<script>
export default {
  computed: {
      newTodo () {
          return this.$store.state.newTodo
      }
  },
  methods: {
      addTodo(newTodo) {
          if(newTodo.title==""){
              this.$store.dispatch('showMsg', "请填写内容!")
              return
          }
          this.$store.dispatch('saveTodo', newTodo)
      }
  }
}
</script>