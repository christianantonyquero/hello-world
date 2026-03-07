<template>
    <div class="store-navigation" :class="{ 'nav-open' : isOpen }">
        <button class="nav-close"  
                            @click="$emit('close')"></button>
        <div class="nav-bg"></div>
        <img src="/img/store/intro-title.png" class="nav-brand"/>
        <nav>
            <!-- navbar-page current -->
            <div class="nav-mobile">
                <a v-for="page in mobileMenu" :key="page.id"
                    href="#"
                    class="navbar-link"
                    :class="hsClass(page)"
                    @click.prevent="onCategoryNavClick(page)">
                    <div class="nav-item-mobile">
                        <div class="nav-item-mobile-icon">
                            <img class="nav-mobile-icon"
                                :src="page.thumbnail_url"
                                :alt="page.name"/>
                        </div>
                        <h2 class="nav-mobile-title">{{page.name}}</h2>
                    </div>
                </a>
            </div>
            <div class="navbar-list nav-desktop" v-for="page in menu" :key="page.id">
                <router-link 
                    class="navbar-page"
                    :class="pageClass(page)"
                    :to="{
                        name: 'Page',
                        params: { slug: page.slug },
                    }"
                    @click.native="onPageNavClick(page)">
                    {{page.name}}
                 </router-link>

                <a v-for="hotspot in page.hotspots.filter((v,i,a)=>a.findIndex(t=>(t.name===v.name))===i)" :key="hotspot.id"
                    href="#"
                    class="navbar-link" 
                    :class="hsClass(hotspot)"
                    @click.prevent="onHotspotNavClick(hotspot)">
                    {{hotspot.name}}
                </a>
            </div>

            <footer class="profile-footer">
                <div class="container">
                    <div class="footer-navbar">
                        <a href="https://lana-del-rey-village.myshopify.com/pages/privacy-and-terms-of-use" target="_blank">Privacy &amp; Terms</a>
                        <div class="copyright">
                            -
                        </div>
                        <a href="https://lana-del-rey-village.myshopify.com/pages/faq" target="_blank">FAQ</a>
                        <div class="copyright">
                            -
                        </div>
                        <a href="https://lana-del-rey-village.myshopify.com/pages/contact-us" target="_blank">Contact Us</a>
                    </div>
                    <div class="copyright">
                        <a href="/email-us" target="_blank">Feedback</a>
                    </div>
                    <div class="copyright">
                        Copyright 2021 - Spumoni Popups
                    </div>
                </div>
            </footer>
        </nav>
    </div>
</template>

<script>
import HotSpotNavigateMixin from '../mixins/HotSpotNavigateMixin';

export default {
    name: 'StoreNavigation',
    mixins: [HotSpotNavigateMixin],
    props: {
        isOpen: {
            type: Boolean,
            required: true,
            default: false,
        },
        menu: {
            type: Array,
            required: true,
        },
        mobileMenu: {
            type: Array,
            required: true,
        }
    },
    data() {
        return {

        };
    },
    methods: {
        hsClass(hotspot) {
            console.log('route name', this.$route.name, this.$route.params.slug, hotspot.value);
            let activeCssClass = '';
            if( this.$route.name == 'ProductListing' && 
                hotspot.type == 'PRODUCT_CATEGORY' &&
                this.$route.params.category_id == hotspot.value ) {
                activeCssClass = 'current';
            } else if( this.$route.name == 'ProductDetail' && 
                hotspot.type == 'PRODUCT' &&
                this.$route.params.product_id == hotspot.value) {
                activeCssClass = 'current';
            } else if( this.$route.name == 'Page' && 
                hotspot.type == 'PAGE' &&
                this.$route.params.slug == hotspot.value) {
                activeCssClass = 'current';
            } else if( this.$route.name == 'PhotoBooth' && 
                hotspot.type == 'PHOTO-BOOTH') {
                activeCssClass = 'current';
            }
            console.log("hotspot.value",hotspot);
            return `${hotspot.slug ? hotspot.slug : ''} hs-type-${hotspot.type} ${this.$route.name} ${activeCssClass}`;
        },
        pageClass(page) {
            let slug = page.slug;
            if( slug == '/' ) {
                slug = 'HOME'
            }
            return 'page-'  + slug;
        },
        // Navigation click methods, close the nav before navigating
        onPageNavClick(page) {
            this.$emit('close');
        },
        onHotspotNavClick(hotspot) {
            console.log("hotspot",hotspot);
            this.$emit('close');
            this.navigate(hotspot);
        },
        onCategoryNavClick(category) {
            console.log("category", category);
            this.$emit('close');
            this.navigateCategory(category);
        }
    }
}
</script>

<style>

</style>