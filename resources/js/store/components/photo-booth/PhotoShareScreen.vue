<template>
    <div class="photo-share-screen">
        <div class="columns">
            <div class="column is-one-half photo-column">
                <div class="photo-container">
                    <img class="photo" :src="photo.image_url"/>
                </div>
            </div>

            <div class="column sharing-column">
                <h4 class="is-size-4 mb-4">Share Your Photo Moment</h4>
    
                <div class="buttons vertical sharing-buttons">
                    <social-share-buttons
                        :networks="social_networks"
                        :share-url="photo.sharing_url"
                        :share-media="photo.image._url"
                        :share-title="share_title"
                        :share-description="share_description" />
                    
                    <button class="button btn-copy-link"
                        type="button"
                        @click="copyLink">Copy Link</button>

                     <a class="button btn-download"
                        :href="photo.image_url" target="_blank" 
                        download="my-photo-moment.jpg">Download Photo</a>

                    <button class="button btn-take-new" type="button"
                        @click="$emit('takeNewPhoto')">Take a New Photo</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import SocialShareButtons from '../../../frontend/components/SocialShareButtons';
import axios from 'axios';

export default {
    name: "PhotoShare",
    props: {
        photo: {
            type: Object,
            required: true,
        }
    },
    components: {
        SocialShareButtons,
    },
    data() {
        return {
            social_networks: [
                // Network comes from the vue-social-sharing docs
                { network: "facebook", link_text: "Facebook", css_classes: "facebook"},
                { network: "twitter", link_text: "Twitter", css_classes: "twitter"},
            ],
        }
    },
    created() {

    },
    mounted() {

    },
    methods: {
        async copyLink() {
            try {
                const result = navigator.clipboard.writeText(this.photo.sharing_url);
                this.$toast.success('Photo Sharing Link Copied');
                // Google track the share
                this.$gtag.gaEvent('share', {
                    content_type: 'page',
                    item_id: this.photo.sharing_url,
                    method: 'Copy to Clipboard',
                });
            }
            catch(error) {
                this.$toast.error('Error copying link to the clipboard');
            }
        },
       
        async downloadClick(filename, event) {
            // user clicks the download image button
            try {
                await this.downloadImage(this.photo.image_url, filename);
                // prevent the 
                event.preventDefault();
            }
            catch(error) {
                // unable to download file so open it
                
            }
            finally {
                // Google track the share
                this.$gtag.gaEvent('share', {
                    content_type: 'page',
                    item_id: this.photo.image_url,
                    method: 'Download Photo',
                });
            }
        },
         /** This code does not work with CORS  */
        async downloadImage(url, filename) {
            try {
                const response = await axios.get(url, {responseType: 'blob', crossDomain: true });
                const blob = new Blob([response.data]);
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                link.click();
                URL.revokeObjectURL(link.href);
            }
            catch(error) {
                console.log('Error Download Image');
                console.log(error);
                throw error;
            }
        }
        
    },
    computed: {
        // Store VUEX
        store() {
            return this.$store.state.store;
        },
        share_title() {
            return `Check out my ${this.store.name} Photo Moment`;
        },
        share_description() {
            return ""
        },
    }
}
</script>

<style>

</style>