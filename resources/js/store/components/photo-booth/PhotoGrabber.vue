<template>
    <div class="photo-grabber">
        <camera ref="camera" 
            @photoTaken="onPhotoTaken"
            @cameraOpen="onCameraOpen"
            @cameraClose="onCameraClose"
            @cameraError="onCameraError">
        </camera>
        
        <div class="crop-wrapper" 
            v-if="canCrop" key="cropWrapper">

            <vue-cropper
                class="cropper"
                ref="cropper"
                :src="cameraImage"
                :guides=true
                :aspectRatio="cropAspectRatio"
                :background=true 
                :responsive=true
                :movable=true
                :rotatable=false
                :view-mode=2
                drag-mode="crop"
                :auto-crop-area=1
                :min-container-width=250
                :min-container-height=300
                :min-canvas-height=400
                :min-crop-box-height=100>
            </vue-cropper>
            
            <button type="button" @click.prevent="onCropClick"
                class="button is-primary is-small btn-crop">Crop</button>
        </div>
    </div>
</template>

<script>
import Camera from './Camera.vue';

import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';

export default {
    // A tool to let a user grab a photo and crop it
    name: "PhotoGrabber",
    components: {
        Camera,
        VueCropper,
    },  
    props: {
        cropWidth: {
            type: Number,
            required: false,
        },
        cropHeight: {
            type: Number,
            required: false,
        },
        cropping_enabled: {
            type: Boolean,
            default: true, 
        }
    },
    data() {
        return {
            is_camera_open: false,
            error: null,
            cameraImage: null,
            croppedImage: null,
        }
    },
    computed: {
        cropAspectRatio() {
            return this.cropWidth / this.cropHeight;
        }, 
        canCrop() {
            return this.cameraImage && this.cropping_enabled
        }
    },
    created() {
        console.log('Photo Grabber Created');

        if(this.cropping_enabled && this.cropWidth == null && this.cropHeight==null) {
            this.error = "Cropping Width and Height required when cropping is enabled"
            this.$emit('error', this.error);
        }

    },
    mounted() {
        console.log('Photo Grabber Mounted');
        this.cameraInit();
    },
    beforeDestroy() {
        this.$refs.camera.closeCamera();
    },
    methods: {
        cameraInit() {
            console.log('Camera Init');
            this.is_camera_open = false;
            this.error = null;
            this.cameraImage = null;
            this.croppedImage = null;
            this.$refs.camera.openCamera();
        },
        cameraClose() {
            this.$refs.camera.closeCamera();
        },
        // Camera Events
        onPhotoTaken(photo) {
            this.cameraImage = photo;
            this.croppedImage = null;
            this.$emit('photoTaken', this.cameraImage);
        },
        onCameraOpen() {
            // the camera state events need to emit back
            this.is_camera_open = true;
        },
        onCameraClose() {
            this.is_camera_open = false;
        },
        onCameraError(err_msg) {
            this.error = err_msg;
            this.$emit('error', err_msg);
        },

        // Image Cropping
        onCropClick() {
            // this needs to emit back the final photo
            console.log('PhotoGrabber onCropClick');

            this.croppedImage = this.$refs.cropper.getCroppedCanvas({
                width: this.cropWidth, height: this.cropHeight
            }).toDataURL();

            this.$emit('photoCropped', this.croppedImage);
        }, 
    }
}
</script>