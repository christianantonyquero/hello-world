<template>
    <div class="product-image-gallery">
        <div class="product-image-border">
            <div class="selected-image">
                <transition name="fade" mode="out-in" appear>
                    <img v-if="selected_image" 
                        :key="selected_image.id"
                        :src="selected_image.file_url" alt="" />
                </transition>
            </div>
        </div>


        <nav class="image-nav" v-if="has_multiple_images">
            <div class="image-nav-column" v-for="(image, idx) in images" :key="idx">
                <div class="image-nav-item" 
                    :class="{'selected':  (image === selected_image)}"
                    @click="viewImage(image)">
                    <div class="image-nav-item-image"
                        :key="idx"
                        :style="{ backgroundImage: `url(${image.file_url})`}"></div>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    name: "ProductImageGallery",
    props: {
        images: {
            type: Array,
            default: null
        }
    },
    mounted() {
         if(this.images && this.images.length > 0) {
            this.selected_image = this.images[0];
        } else {
            this.selected_image = null;
        }
    },
    data(){
        return {
            selected_image: null,
        }
    },
    computed: {
        has_multiple_images() {
            return this.images.length > 1;
        },
    },
    watch: {
        images: function(newImages) {
            if(newImages && newImages.length > 0) {
                this.selected_image = newImages[0];
            } else {
                this.selected_image = null;
            }
        }
    },
    methods: {
        viewImage(image) {
            console.log("viewImage() click - " + image);
            this.selected_image = image;
        }
    }
}
</script>

<style scoped lang="scss">

</style>