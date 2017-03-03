<template>
  <div id="todos">
    <h1>备忘录数量（{{todoCount}}）</h1>
    <ul class="list-group">
      <li class="list-group-item" v-bind:class="{ 'completed' : todo.completed }" v-for="(todo, index) in todos">

        <router-link :to="{ name: 'todo', params: { id: todo.id }}">{{todo.title}}</router-link>
        <button class="btn btn-xs pull-right btn-warning" style="margin-left:5px;" v-on:click="deleteTodo(index, todo)">删除</button>
        <button class="btn btn-xs pull-right" v-on:click="toggleCompletion(todo)" v-bind:class="[todo.completed ? 'btn-danger' : 'btn-success' ]">{{ todo.completed ? '取消' : '标记' }}</button>
      </li>
    </ul>

    <todo-form></todo-form>
  </div>
</template>

<style>
.completed{
    color:green;
    text-decoration: line-through;
}
</style>
<script>
import TodoForm from './TodoForm'

export default {
  name: 'todos',
  mounted() {
      this.$store.dispatch('getTodos')
  },
  methods: {
      deleteTodo(index, todo) {
          this.$store.dispatch('removeTodo', { todo: todo, index: index })
      },
      toggleCompletion(todo){
          this.$store.dispatch('completeTodo', todo)
      }
  },
  computed: {
      todos () {
          return this.$store.state.todos
      },
      todoCount() {
          return this.$store.state.todos.length;
      }
  },
  components: {
      TodoForm
  }
}
</script>