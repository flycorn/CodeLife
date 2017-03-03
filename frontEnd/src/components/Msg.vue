<template>
    <div class="alert" v-bind:class="msgType">
        <strong>{{msgText}}</strong>
    </div>
</template>
<script>
    export default{
        computed: {
            msgType () {
               return this.$store.state.msgType
            },
            msgText () {
               return this.$store.state.msgText
            },
        }
    }
</script>
