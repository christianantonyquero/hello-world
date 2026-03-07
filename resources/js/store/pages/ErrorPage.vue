<template>
  <div class="error-page"
       :class="[errorClass]">
    
    <p class="error-message">{{errorMsg}}</p>
  </div>
</template>

<script>
export default {
    props: {
    },
    data: function() {
        return {

        }
    },
    computed: {
        errorType() {
            return this.$store.state.appError.errorType;
        },
        errorMsg() {
            return this.$store.state.appError.errorMsg;
        },
        errorClass() {
            return 'error-' + this.errorType;
        }
    }
}
</script>

<style lang="scss" scoped>
    .error-page {
        width: 100%;
        height: 100vh;
        background-color: red;
        color: white;
        display: flex;
        justify-content:  center;
        align-items: center;
    }
</style>