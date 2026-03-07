<template>
    <div class="modal is-active" v-if="show">
        <div class="modal-background"></div>
            <div class="modal-content">
                <slot></slot>
            </div>
            <button class="modal-close is-large" aria-label="close"
                    v-if="closeButton"
                    @click="$emit('close')"></button>
    </div>
</template>

<script>
export default {
    name: "Modal", // A Bluma based modal
    props: {
        show: {
            type: Boolean,
            required: true,
            default: false,
        },
        closeButton: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {

        };
    },
    computed: {

    },
    methods: {

    }
}
</script>

<style lang="scss" scoped>
    
</style>