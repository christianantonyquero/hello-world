import Vue from 'vue';

import SocialShareButtons from './components/SocialShareButtons';
import gtagHelpers from '../store/services/gtag-mixin';

// hook in some gtag functions
Vue.prototype.$gtag = gtagHelpers;

const sharing_app = new Vue({
    el: '#social-share-app',
    name: 'SocialSharingApp',
    components: {
        SocialShareButtons,
    },
    data: {
        photo: null,
        share_title: "Check out this Lana Del Rey Photo Moment",
        share_description: "Explore the world of Lana Del Rey and get access to exclusive merch!",
        social_networks: [
            // Network comes from the vue-social-sharing docs
            {
                network: "facebook",
                link_text: "Facebook",
                css_classes: "facebook"
            },
            {
                network: "twitter",
                link_text: "Twitter",
                css_classes: "twitter"
            },
        ],
    },
    created() {
        this.photo = window.__INIT_STATE__.photo;
    },
    mounted() {
        console.log('social sharing app loaded');
    }
});