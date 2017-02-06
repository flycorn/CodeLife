<template>
  <div id="app">

    <loader></loader>

    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="pull-left" style="padding: 6px 12px">我的备忘录</div>

            <div class="dropdown pull-right" v-show="token">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                {{ user.name }}
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a v-on:click="logout()">退出</a></li>
              </ul>
            </div>

          </div>
          <div class="panel-body">

              <msg v-show="msg"></msg>

              <router-view></router-view>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Loader from './components/Loader'
import Msg from './components/Msg'

export default {
  name: 'app',
  computed: {
      token () {
          return this.$store.state.token
      },
      user () {
          return this.$store.state.user
      },
      msg () {
          return this.$store.state.msg
      }
  },
  methods: {
      //退出
      logout (){
          this.$store.dispatch('logout')
      }
  },
  components: {
      Loader,
      Msg,
  }
}
</script>

