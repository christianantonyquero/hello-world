<template>
    <div class="hotspot-wrapper">
        <a
            v-if="!showVideo && !hideLink"
            :key="hotspot.id"
            @click.prevent="clickHotSpot"
            class="hotspot"
            :class="hsClass"
            :style="{ left: hotspot.x + '%', top: hotspot.y + '%' }">
            <span class="name">{{ hotspot.name }}</span>
        </a>
        <transition name="hotspot-video-transition" mode="in-out">
            <video v-if="showVideo"
                :key="hotspot.id + '-video'"
                preload="auto"
                autoplay muted playsinline
                class="hotspot-video"
                @ended="onVideoEnd">
                <source :src="hotspot.video" type="video/mp4"/>
            </video>
        </transition>
    </div>
</template>

<script>
import HotSpotNavigateMixin from '../mixins/HotSpotNavigateMixin';

export default {
    name: "Hotspot",
    mixins: [HotSpotNavigateMixin],
    props: {
        hotspot: {
            type: Object,
            required: true,
        },
        hideLink: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    data() {
        return {
            isVideoPlaying: false,
            HOTSPOT_MOBILE_BREAKPOINT: 768, // responsive size for mobile
        }
    },
    created() {
    },
    methods: {
        clickHotSpot(event) {

            document.querySelector(".store-page").scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });

            if(this.hasVideo && 
               this.viewportWidth() >= this.HOTSPOT_MOBILE_BREAKPOINT) {
                // HS has a video, play it before navigationg
                this.playVideo();
            } else {
                this.navigate(this.hotspot);
            }
        },
        playVideo() {
            this.$emit('videoStart', this.hotspot);
            this.isVideoPlaying = true;
        },
        onVideoEnd() {
            // Video stopped, navigate
            console.log('HotSpot - onVideoEnd()');
            this.$emit('videoEnd', this.hotspot);
            this.isVideoPlaying = false;
            this.navigate(this.hotspot);
        },
        // Viewport stuff
        viewportWidth() {
            return Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
        },
        viewportHeight() {
            return Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        }
        
    },
    computed: {
        hsClass() {
            let video_class_name = this.hasVideo ? 'hs-has-video' : '';
            return `${this.hotspot.slug ? this.hotspot.slug : ''} hs-type-${this.hotspot.type} ${video_class_name}`;
        },
        hasVideo() {
            return this.hotspot.video ? true : false;
        },
        showVideo() {
            return this.hasVideo && this.isVideoPlaying;
        },
    },
};
</script>

