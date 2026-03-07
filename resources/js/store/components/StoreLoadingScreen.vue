<template>
    <div class="store-loading-screen">
        <p class="loading-msg">Loading....</p>
        <p v-if="num_items" class="loaded-precent">{{ loaded_percent}}<span>%</span></p>
    </div>
</template>

<script>
export default {
    name: "StoreLoadingScreen",
    props: {
        num_items: {
            type: Number,
            default: null,
            required: false,
        },
        loaded_cnt: {
            type: Number,
            default: 0,
            required: false,
        }
    },
    computed: {
        loaded_percent() {
            if(this.num_items) {
                return Math.round((this.loaded_cnt / this.num_items) * 100);
            } else {
                return null;
            }
        }
    },
}
</script>

<style lang="scss" scoped>

</style>