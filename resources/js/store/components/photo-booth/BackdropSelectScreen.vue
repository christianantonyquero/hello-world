<template>
    <div class="photobooth-columns">
        <div class="photobooth-image-container" ref="imageStage">
            <img class="size-helper" src="/img/photobooth/photo-helper.jpg" width="500" height="500">
            <img v-if="selected_backdrop"
                :key="selected_backdrop.id"
                :src="selected_backdrop.image_url"
                crossorigin="anonymous"
                ref="selected_backdrop_el"
                class="backdrop-img container-content"/>
            <img class="user-image container-content"  :src="userImage" key="user-image" ref="user_image_on_stage_el" crossorigin="anonymous"/>
        </div>
        <div class="backdrop-picker">
                <h5 class="picker-title">Choose a Backdrop</h5>
                <div class="backdrop-list">
                    <div class="columns is-multiline">
                        <div class="column is-half" v-for="backdrop in backdrops" >
                            <img 
                                :key="backdrop.id" :src="backdrop.image_url" 
                                class="backdrop-img"
                                crossorigin="anonymous"
                                :class="{ selected: backdrop == selected_backdrop}"
                                @click="onBackdropClick(backdrop)"/>
                        </div>
                    </div>
                </div>
                <div class="buttons vertical">
                    <button class="button is-light retake" @click="$emit('retakePhoto')">Retake</button>
                    <button class="button is-primary save" @click="makeUserPhoto">Save</button>
                </div>
            </div>
    </div>

</template>

<script>
export default {
    name: "PhotoBoothBackdropSelectScreen",
    props: {
        backdrops: {
            type: Array,
            required: true,
        },
        userImage: {
            type: String,
            required: true,
        },
        imageWidth: {
            type: Number,
            required: true,
        },
        imageHeight: {
            type: Number,
            required: true,
        }
        
    },
    data() {
        return {
            selected_backdrop: null,
        }
    },
    mounted() {
        this.selected_backdrop = this.backdrops[0];
    },
    methods: {
        onBackdropClick(backdrop) {
            console.log('onBackdropClick');
            this.selected_backdrop = backdrop;
        },
        // Create the composed user photo client side
        makeUserPhoto() {
            console.log('makeUserPhoto()');
            let outCanvas =  document.createElement('canvas');
            outCanvas.width = this.imageWidth;
            outCanvas.height = this.imageHeight;
            
            let ctx = outCanvas.getContext('2d');
            ctx.drawImage(this.$refs.selected_backdrop_el, 0, 0);
            ctx.drawImage(this.$refs.user_image_on_stage_el, 0, 0);

            let outImage =  outCanvas.toDataURL();
            
            this.$emit('savePhoto', outImage);
        },
    }
}
</script>

<style>

</style>