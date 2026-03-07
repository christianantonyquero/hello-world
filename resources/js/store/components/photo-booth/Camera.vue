<template>
    <div class="photobooth-image-container">
        <img class="size-helper" src="/img/photobooth/photo-helper.jpg" width="500" height="500"/>
        <div class="photobooth-loading">Loading Camera. Please Wait...</div>
        <video 
            class="camera-viewfinder container-content"
            ref="camera" 
            v-show="is_camera_open"
            autoplay muted playsinline disablePictureInPicture>
        </video>
        <button 
            v-if="canTakePhoto"
            class="btn-take-photo"
            type="button"
            @click="takePhoto">Take Photo  
        </button>
        <div class="countdown">
            <span>3</span>
            <span>2</span>
            <span>1</span>
            <img src="img/photobooth/camera-circle.svg"/>
        </div>
    </div>
</template>

<script>
export default {
    name: "Camera",
    props: {
    },
    data: function() {
        return {
            is_camera_loading: false,
            is_camera_open: false,
    
            error: null,

            photo: null,
            camCanvas: null,
        }
    },
    created() {
        // Create an off screen canvas element for capturing our photo
        if(this.camCanvas == null) {
            this.createCamCanvas();
        }
    },
    methods: {
        createCamCanvas() {
            this.camCanvas = document.createElement('canvas');
        },
        // Webcam stuff
        async toggleCamera() {
            if( this.is_camera_open ) {
                // Close the camera
                this.closeCamera();
            } else {
                // Open the camera
                this.openCamera();
            }
        },
        async openCamera() {
            console.log('open camera');

            this.error = null; // clear camera errors;
            
            this.is_camera_loading = true;

            const constraints =  {
                audio: false,
                video: { 
                    facingMode: "user", // front facing camera on phones
                }, 
            };

            try {
                let stream = await navigator.mediaDevices.getUserMedia(constraints);
                this.is_camera_loading = false;
                this.is_camera_open = true;
                this.$refs.camera.srcObject = stream;

                this.$emit('cameraOpen');
            }
            catch(error){
                this.is_camera_loading = false;
                let err_msg = ""
                if(error.name == "NotFoundError" || error.name == "DevicesNotFoundError") {
                    // required track is missing 
                    err_msg += "No Camera Found";
                } else if (error.name == "NotReadableError" || error.name == "TrackStartError") {
                    // webcam or mic are already in use 
                    err_msg += "The Camera is Already In-Use";
                } else if (error.name == "OverconstrainedError" || error.name == "ConstraintNotSatisfiedError") {
                    // constraints can not be satisfied by avb. devices 
                    err_msg += "The Camera is unable to meet the applications requirements";
                } else if (error.name == "NotAllowedError" || error.name == "PermissionDeniedError") {
                    // permission denied in browser 
                    err_msg += "You must allow access to the Camera to use this feature.";
                } else if (error.name == "TypeError" || error.name == "TypeError") {
                    // empty constraints object 
                    err_msg += "Camera Error: Empty Media Constraints";
                } else {
                    // other errors 
                    err_msg += "Unspecified Camera Error";
                }
                
                console.log(err_msg);
                console.log(error.message);

                this.error = err_msg;
                this.$emit('cameraError', err_msg);
            }
        },
        async closeCamera() {
            console.log('close camera');
            this.error = null; // clear camera errors
            this.is_camera_loading = false;
            this.is_camera_open = false;
            
            if(this.$refs.camera !== null && 
               this.$refs.camera.srcObject !== null) {
                let tracks = this.$refs.camera.srcObject.getTracks();
                tracks.forEach(track => {
                    track.stop();
                });
            }
            
            this.$emit('cameraClose');
        },
        takePhoto() {
            console.log('take photo');
            
            const videoWidth = this.$refs.camera.videoWidth;
            const videoHeight = this.$refs.camera.videoHeight;
            this.camCanvas.width = videoWidth
            this.camCanvas.height = videoHeight;

            const context = this.camCanvas.getContext('2d');
            const countdown = document.querySelector(".countdown");
            countdown.classList.add("active");

            setTimeout(function () {
                context.drawImage(this.$refs.camera, 0, 0);//, this.width, this.height);
                this.photo = this.camCanvas.toDataURL();
                this.closeCamera();
                this.$emit('photoTaken', this.photo);
            }.bind(this), 3200)

        }
    },
    computed: {
        canTakePhoto() {
            return this.is_camera_open && !this.is_camera_loading;
        }
    }
}
</script>

<style lang="scss" scoped>
    
</style>