<template>
    <div class="photo-booth-page" key="photo-booth-page"
         :class="[currentPageClass, otherPageClasses]" 
         :style="pageStyles">

        <section class="photo-booth-wrapper">
            <header class="photo-booth-header">
            </header>

            <div class="photo-booth">
                <div v-if="hasErrors" class="notification is-danger mb-4">
                    <p v-for="(err, idx) in errors" :key="idx">{{err}}</p>
                </div>

                 <transition name="simple-fade" mode="out-in" >
                    <section class="photobooth-image-container" v-if="is_processing" key="is-processing-screen">
                        <img class="size-helper" src="/img/photobooth/photo-helper.jpg" width="500" height="500">
                        <div class="processing container-content">
                            <trinity-rings-spinner
                                class="mb-6"
                                :animation-duration="1000"
                                :size="80"
                                :color="'#E3C3BC'"/>

                            <h4 class="is-size-3 mb-2 has-text-primary">Processing</h4>
                            <p>Please Wait</p>
                        </div>
                    </section>    
                    <template v-else>
                        <start-screen key="start-screen" 
                            v-if="app_screen == APP_SCREENS.START" 
                            @openCamera="openCamera"/> -->

                        <section key="camera-screen" 
                                class="camera-screen" 
                                v-if="app_screen == APP_SCREENS.CAMERA">
                            <photo-grabber 
                                ref="camera"
                                :cropping_enabled="false"
                                :cropWidth="imageWidth"
                                :cropHeight="imageHeight"
                                @photoTaken="onPhotoTaken"
                                @photoCropped="onPhotoCropped"
                                @error="onCameraError"
                                @cancel="closeCamera">
                            </photo-grabber>
                        </section>

                        <review-photo-screen key="review-photo-screen" 
                            v-if="app_screen == APP_SCREENS.REVIEW_PHOTO"
                            :photo="userImage"
                            @retakePhoto="retakePhoto"
                            @usePhoto="usePhoto" />
                            

                            <!-- v-if="app_screen == APP_SCREENS.BACKDROP_SELECT" -->
                        <backdrop-select-screen key="backdrop-select-screen"
                            v-if="app_screen == APP_SCREENS.BACKDROP_SELECT"
                            :backdrops="backdrops" 
                            :userImage="userImage"
                            :imageWidth="imageWidth"
                            :imageHeight="imageHeight"
                            @retakePhoto="retakePhoto"
                            @savePhoto="savePhoto" />

                        
                        <photo-share-screen key="photo-share-screen"
                            v-if="app_screen == APP_SCREENS.SHARE"
                            :photo="outputImage" 
                            @takeNewPhoto="takeNewPhoto" />
                        
                    </template>
                 </transition>

            </div> <!-- .photo-booth-->
        </section><!-- .photo-booth-wrapper -->
    </div><!-- .photo-booth-page -->
</template>

<script>
import Modal from '../components/Modal.vue';
import PhotoGrabber from '../components/photo-booth/PhotoGrabber.vue';
import StartScreen from '../components/photo-booth/StartScreen.vue';
import ReviewPhotoScreen from '../components/photo-booth/ReviewPhotoScreen.vue';
import BackdropSelectScreen from '../components/photo-booth/BackdropSelectScreen.vue';
import PhotoShareScreen from '../components/photo-booth/PhotoShareScreen.vue';
import { TrinityRingsSpinner } from 'epic-spinners';

const APP_SCREENS = Object.freeze({
    "START": 'START', 
    "CAMERA": 'CAMERA',
    "REVIEW_PHOTO": 'REVIEW_PHOTO', 
    "BACKDROP_SELECT": 'BACKDROP_SELECT', 
    "SHARE": 'SHARE',
});

export default {
    name: "PhotoBooth",
    components: {
        Modal,
        StartScreen,
        PhotoGrabber,
        ReviewPhotoScreen,
        BackdropSelectScreen,
        PhotoShareScreen,
        TrinityRingsSpinner,
    },
    data() {
        return {
            errors: [],
            // State Flags
            is_processing: false,

            userImage: null,
            outputImage: null,


            imageWidth: 1080, // 1080x1080
            imageHeight: 1080,

            backdrops: [],

            APP_SCREENS: APP_SCREENS,
            app_screen: APP_SCREENS.START,
        };
    },
    computed: {
        // Store VUEX
        store() {
            return this.$store.state.store;
        },
        aspectRatio() {
            return this.imageWidth / this.imageHeight;
        }, 
        pageBackgroundImage() {
            console.log('page background image');
            // Pull Background image from the store object in vuex state
            if( this.store && this.store.photo_booth_bg_image ) {
                return this.store.photo_booth_bg_image_url;
            } else {
                return "";
            }
        },
        pageStyles() {
            let styles = {};
            if( this.pageBackgroundImage ) {
                styles['backgroundImage']  = `url(${this.pageBackgroundImage})`;
            }
            return styles;
        },
        currentPageClass() {
            return `current-screen-${this.app_screen}`;
        },
        otherPageClasses() {
            return {
                'has-errors': this.hasErrors,
                'is-processing': this.is_processing,
            }
        },
        hasErrors() {
            return this.errors.length > 0;
        }
        
    },
    created() {
        this.errors = [];
    },
    mounted() {
        this.loadBackdrops();
        this.showStartScreen();
    },
    methods: {
        // Screens
        showStartScreen() {
            this.app_screen = this.APP_SCREENS.START;
        },
        showCameraScreen() {
            this.app_screen = this.APP_SCREENS.CAMERA;
        },
        showReviewPhotoScreen() {
            this.app_screen = this.APP_SCREENS.REVIEW_PHOTO;
        },
        showBackdropSelectScreen() {
            this.app_screen = this.APP_SCREENS.BACKDROP_SELECT;
        },
        showShareScreen() {
            this.app_screen = this.APP_SCREENS.SHARE;
        },
        
        fakeShareScreen() {
            this.outputImage = {
                "id": 6,
                "sharing_id": "92c381a2-b2ea-4e96-8b6c-e64f5c0af70c",
                "image": "photo-booth/user-images/1613683912602edcc801ede.jpg",
                "caption": null,
                "user_id": 1,
                "store_id": 1,
                "created_at": "2021-02-18T21:31:52.000000Z",
                "updated_at": "2021-02-18T21:31:52.000000Z",
                "deleted_at": null,
                "image_url": "https://spumoni-ldr.test//storage/photo-booth/user-images/1613683912602edcc801ede.jpg",
                "sharing_url": "https://spumoni-ldr.test/photo-booth/92c381a2-b2ea-4e96-8b6c-e64f5c0af70c"
            };
            this.showShareScreen();
        },
        
        // Webcam Methods and Events
        openCamera() {
            this.errors = [];
            this.showCameraScreen();
            console.log('openCamera');
        },
        closeCamera() {
            // Camera was closed without taking a photo
            this.showStartScreen();
            console.log('closeCamera');
        },
        onPhotoTaken(photo) {
            console.log('PhotoBooth onPhotoTaken');
            this.userImage = photo;
            this.closeCamera();

            this.showReviewPhotoScreen();

        },
        onPhotoCropped(photo) {
            console.log('PhotoBooth onPhotoCropped');
            this.userImage = photo;
            this.closeCamera();
        },
        onCameraError(err_msg) {
            console.log('onCameraError', err_msg);
            this.errors.push(err_msg);
            this.showStartScreen();
        },

        // Review Photo Screen
        retakePhoto() {
            this.openCamera();
        },
        usePhoto() {
            // Process user photo
            this.processUserPhoto();
        },

        // Submit the user photo for processing
        async processUserPhoto() {
            console.log('processUserPhoto')
            this.errors = [];
            this.is_processing = true;

            let formData = new FormData();
            formData.append('userPhoto', this.userImage);

            try {
                console.log('processUserPhoto() success');
                const response = await this.$api.photoBoothProcessUserPhoto(this.store.id, formData);
                console.log('Success Procces Photo');
                console.log(response);
                this.userImage = response.out_image;
                
                this.showBackdropSelectScreen();
            }
            catch(error) {
                console.log('Error processingUserPhoto');
                this.logError('Error Processing Photo', error);
                this.showStartScreen();
            }
            finally {
                this.is_processing = false;
            }
        },

        // Backdrops
        async loadBackdrops() {
            console.log('load backdrops');
            this.backdrops = await this.$api.getPhotoBoothBackdrops(this.store.id);
        },
        // Save the Photo and upload it
        async savePhoto(tmpOutputImage) {
            try {
                console.log('savePhoto() called');

                this.errors = [];
                this.is_processing = true;

                let formData = new FormData();
                formData.append('userPhoto', tmpOutputImage);

                const response = await this.$api.photoBoothSaveUserPhoto(this.store.id, formData);
                console.log('Success Proces Photo');
                console.log(response);
                this.outputImage = response.userPhoto;
                
                this.showShareScreen();
                
            }
            catch(error) {
                console.log('Error savePhoto()');
                this.logError('Error Saving Photo', error);
    
            }
            finally {
                this.is_processing = false;
            }
        },

        takeNewPhoto() {
            // User clicks take new photo on the sharing screen.
            this.showStartScreen();
        },
        logError(msg, error=null) {
            let err_msg = msg;
            console.log(error);
            if(error) {
                if( error.response && error.response.data.message) {
                    err_msg += ' - ' + error.response.data.message;
                }
                this.errors.push(err_msg);
                if(error.response && error.response.data.errors) {
                    for( const prop in error.response.data.errors) {
                        this.errors.push(error.response.data.errors[prop][0]);
                    }
                }
            }
            else {
                this.errors.push(err_msg);
            }
        }
    }
}
</script>