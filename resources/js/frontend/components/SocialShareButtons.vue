<template>
    <div class="social-sharing-buttons">
        <share-network
            v-for="network in networks"
            :key="network.network"
            class="button"
            :class="[network.network, network.css_classes]"
            :network="network.network"
            :url="shareUrl"
            :media="shareMedia"
            :title="shareTitle"
            :description="shareDescription"
            @open="onSocialOpen">
            {{network.link_text}}
        </share-network>
    </div>
</template>

<script>
import {ShareNetwork} from 'vue-social-sharing';
export default {
    components: {
        ShareNetwork,
    },
    props: {
        networks: {
            type: Array,
            required: true,
        },
        shareUrl: {
            type: String,
            required: true,
        },
        shareTitle: {
            type: String,
            required: true,
        },
        shareDescription: {
            type: String,
            default: ''
        },
        shareMedia: {
            type: String,
            default: ''
        }
     },
     methods: {
         onSocialOpen(network_name, shared_url) {
            if( this.$gtag && this.$gtag.gaEvent ) {
                console.log('share');
                this.$gtag.gaEvent('share', {
                    content_type: 'page',
                    item_id: shared_url,
                    method: network_name,
                });
            }
         }
     }
}
</script>

<style>

</style>