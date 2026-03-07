<template>
    <transition name="img-hover-fade" mode="out-in">
        <img 
            :key="is_hovering ? 'hover': 'not-hover'"
            :src="imageToShow"
            @mouseover="onHover"
            @mouseleave="stopHover"/>
    </transition>
</template>

<script>
export default {
    name: "HoverImage",
    props: {
        imageUrl: {
            type: String,
            required: true,
        },
        hoverUrl: {
            type: String,
            default: null,
            required: false,
        }
    },
    data() {
        return {
            is_hovering: false,
        }
    },
    methods: {
        onHover(event) {
            if(this.hoverUrl) {
                this.is_hovering = true;
            }
        },
        stopHover(event) {
            this.is_hovering = false;
        }
    },
    computed: {
        imageToShow() {
            console.log(this.hoverUrl);
            if(this.hoverUrl && this.is_hovering) {
                return this.hoverUrl;
            } else {
                return this.imageUrl;
            }
        }
    }
}
</script>