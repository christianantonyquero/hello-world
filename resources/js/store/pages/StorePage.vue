<template>
    <div class="store-page" 
         key="store-page"
         v-if="store_page"
         :class="[page_slug_class, pageClasses]">

            <transition name="fade" mode="in-out" appear>
                <div v-if="has_intro_video && show_intro_video"
                     key="store-page-intro"
                     class="store-page-body">
                    <video 
                        key="intro-video"
                        class="bg-video intro-video"
                        autoplay  muted playsinline
                        @play="onIntroVideoStart"
                        @ended="onIntroVideoEnd">
                        <source :src="store_page.intro_video" type="video/mp4"/>
                    </video>
                </div>
                <div v-else
                     key="store-page-body"
                     class="store-page-body">
                    <img :src="store_page.background_image_url" class="page-bg"/>
                    <video 
                        v-if="has_background_video"
                        key="bg-video"
                        class="bg-video" autoplay loop muted playsinline
                        :poster="store_page.background_image_url"
                        @ended="onBackgroundVideoEnd">
                        <source :src="store_page.background_video" type="video/mp4"/>
                    </video>
                    
                    <div v-if="has_content"  
                        class="content-wrapper">
                        <div class="content" v-html="store_page.html_content"></div>
                    </div>
            
                    <div v-if="has_hotspots" 
                        class="hotspot-list">
                        <Hotspot
                            v-for="hotspot in store_page.hotspots"
                            :key="hotspot.id"
                            :hotspot="hotspot"
                            :hide-link="hideHotspotLinks"
                            @videoStart="onHotspotVideoStart"
                            @videoEnd="onHotspotVideoEnd"
                            />
                    </div>
                
                </div>
            </transition>

            <portal to="home-button">
                <router-link v-if="store_page.parent"
                    class="btn btn-main-menu btn-text"
                    :to="parentPageRoute">

                    Back <span>to {{store_page.parent.name}}</span>
                </router-link>
            </portal>
    </div> <!-- end store page wrapper -->

    <div v-else class="store-page-wrapper not-loaded" key="no-page">
        <!-- <div v-if="isLoading" key="page-loading">Page Loading &hellip;</div> -->
    </div>
</template>

<script>
import Hotspot from  '../components/Hotspot.vue';

export default {
    name: 'StorePage',
    components: {
        Hotspot
    },
    props: {
        slug: {
            type: String,
            default: null,
        }
    },
    data(){
        return {
            store: window.__INIT_STATE__.store,
            store_page: null,
            errors: [],
            show_intro_video: false,
            hideHotspotLinks: false, // a way to hide the hotspot links when a hot spot video is playing
            HOTSPOT_MOBILE_BREAKPOINT: 768, // responsive size for mobile
        }
    },
    computed: {
        isLoading() {
            return this.$store.state.pagesLoading;
        },
        pageClasses() {
            return {
                'has-content': this.has_content,
                'has-hotspots': this.has_hotspots,
                'has-background-video': this.has_background_video,
                'has-intro-video': this.has_intro_video,
                'has-errors': this.has_errors,
            }
        },
        has_content() {
            return this.store_page && this.store_page.html_content != null;
        },
        has_hotspots() {
            return this.store_page && this.store_page.hotspots.length > 0;
        },
        page_slug_class() {
            if(this.store_page) {
                if(this.store_page.slug == '/') {
                    return 'page-home';
                } else {
                    return 'page-' + this.store_page.slug;
                }
            } else {
                return '';
            }
        },
        has_background_video() {
            return this.store_page && this.store_page.background_video != null;
        },
        has_intro_video() {
            return this.store_page && this.store_page.intro_video != null;
        },
        has_errors() {
            console.log("Errors" + this.errors);
            return this.errors.length > 0;
        },
        hasVideoPlayedKey() {
            return 'store-page-' + this.store_page.id + '-has-intro_played';
        },
        parentPageRoute() {
            if( this.store_page.parent.slug == '/' ||
                this.store_page.parent.slug == '' ) {
                    return {
                        name: 'Home'
                    }
            } else {
                return {
                    name: 'Page',
                    params: { slug: this.store_page.parent.slug },
                };
            }
        },
    },
    beforeRouteUpdate (to, from, next) {
        console.log('StorePage beforeRouteUpdate()');
        this.loadPage(to.params.slug);
        next();
    },
    watch: {
        '$route': function() {
            this.loadPage(this.slug);
            //this.loadPage(this.$route.params.slug);
        },
    },
    created() {
        this.loadPage(this.slug);
        //this.loadPage(this.$route.params.slug);
    },
    methods: {
        async loadPage(slug){
            //this.isLoading = true;
            this.errors = [];
            console.log('loadPage()', 'store id:' + this.store.id, slug);
            try {
                
                this.store_page = this.$store.getters.getPageBySlug(slug);
                if( !this.store_page ) {
                    this.store_page = await this.$store.dispatch('loadPageBySlug', this.slug);
                }

                // Show the intro video if the page has one and we have not played it
                console.log('page canplayIntro()', this.canPlayIntroVideo());
                if( this.canPlayIntroVideo()) {
                    this.show_intro_video = true;
                }

                // Set document title page name and store name
                document.title = this.store_page.name + ' - ' + this.store.name;
            }
            catch(error){
                this.errors.push('Error Loading Store Page');
                console.log(error);
                if(error.response) {
                    this.errors.push(error.response.data.message);
                } 
                this.$toast.open({
                    message: 'Error Loading Store Page',
                    type: 'error',
                    duration: 10000
                });
            }
            finally {
                //this.isLoading = false;
            }
        },
        onBackgroundVideoEnd() {
            //console.log('backgroundVideo end');
        },
        onIntroVideoStart() {
            // Mark the intro video as having played, don't wait until the end
            console.log('onIntroVideoStart');
            this.setIntroVideoPlayed();
        },
        onIntroVideoEnd() {
            console.log('intro video ended');
            this.show_intro_video = false;
        },
        hasIntroVideoPlayed() {
            // check if the intro video has already played
            let hasIntroPlayed = localStorage.getItem(this.hasVideoPlayedKey)
            return hasIntroPlayed
        },
        setIntroVideoPlayed() {
            // Set the intro video already played
            localStorage.setItem(this.hasVideoPlayedKey, true);
        },
        // Hotspot events
        onHotspotVideoStart(hotspot) {
           // console.log('onHotspotVideoStart', hotspot);   
            this.hideHotspotLinks = true; 
        },
        onHotspotVideoEnd(hotspot) {
            console.log('onHotspotVideoEnd', hotspot);
            this.hideHotspotLinks = false;
        },
        canPlayIntroVideo() {
            return this.store_page.intro_video &&
                   this.viewportWidth() >= this.HOTSPOT_MOBILE_BREAKPOINT &&
                   !this.hasIntroVideoPlayed()
        },
        // Viewport stuff
        viewportWidth() {
            return Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
        },
        viewportHeight() {
            return Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        }
    }
}
</script>

<style scoped lang="scss">
    
</style>
