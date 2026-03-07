 <template>
         <div id="store-app" :class="[appClasses]">
         <store-navigation 
             :is-open="isMenuOpen"
             @close="onMenuClose"
             :menu=menu
             :mobileMenu=mobileMenu></store-navigation>
 
                 <div class="store-app-wrapper">
 
                         <header class="store-page-header">
                                 <div class="header-content-wrapper">
                                         <nav class="nav left-nav">
                                                 <a role="button" class="nav-toggle" :class="{ 'active' : isMenuOpen }" 
                             @click.prevent="toggleMenu"></a>
 
                         <portal-target name="home-button">
                                                                 <router-link 
                                                                         v-if="!isHomeRoute"
                                                                         :to="{name: 'Home'}" class="btn btn-main-menu btn-text">Back</router-link>
                                                 </portal-target>
                                         </nav>
 
                                         <nav class="nav right-nav">
                                                         <router-link
                                                                 :to="{name: 'Products'}"
                                                                 class='btn-search'>
                                                                 <span>Search</span>
                                                         </router-link>
                                                         <router-link 
                                                                 :to="{name: 'Cart'}"  
                                                                 class='btn-cart'
                                                                 :class="{ 'has-items-in-cart': hasItemsInCart}">
                                                                 <span>cart</span>
                                                                 <p class="cart-qty">{{cartTotalQuantity}}</p>
                                                         </router-link>
 
                                                 <div class="user-dropdown" v-if="user">
                                                         <a href="/profile" class="user-photo" v-if="user.profile_photo_url" :style="{ backgroundImage: `url('${user.profile_photo_url}')` }"></a>
                                                         <a href="/profile" class="user-photo" v-else></a>
                                                         <div class="user-dropdown-nav">
                                                                 <a href="/profile">Profile</a>
                                                                 <a href="#" @click.prevent="logout">Logout</a>
                                                         </div>
                                                 </div>
                                         </nav>
                                 </div>
                         </header>
 
                         <transition mode="in-out" appear :name="storeRouterTransition">
                                 <router-view class="store-router-page-top-level" 
                                                         :key="$route.fullPath" />
                         </transition>
 
                         <!-- v-if does not work, init sound wont play, v-show works -->
                         <!--
                         <div class="audio-player-wrapper" v-show="hasSongList" key="audio_player_wrapper">
                                 <audio-player :song_list="store_audio.song_list" key="audio_player"
                                                         :dark_mode="store_audio.dark_mode"></audio-player>
                         </div>
                         -->
 
                         <div class="audio-player-wrapper spotify-audio-player">
                                 <div class="audio-player">
                                         <span>Loading...</span>
                                         <iframe src="https://open.spotify.com/embed?uri=spotify%3Atrack%3A4U9ACZETPOqGuJkWtjkjLF" width="640" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>
                                 </div>
                         </div>
 
                         <store-loading-screen 
                                 v-if="pre_load.is_preloading"
                                 :num_items="pre_load.num_images"
                                 :loaded_cnt="pre_load.img_load_cnt">
                         </store-loading-screen>
                 </div>
         </div>
 </template>
 
 <script>
   import axios from 'axios';
   import AudioPlayer from '../frontend/components/AudioPlayer';
   import StoreLoadingScreen from '../store/components/StoreLoadingScreen';
   import StoreNavigation from '../store/components/StoreNavigation';
   import uniq from "lodash/uniq";
 
   export default {
     components: {
         AudioPlayer,
         StoreLoadingScreen,
         StoreNavigation,
     },
     data() {
         return {
             errors: [],
             store_audio: {
               song_list: [],
               dark_mode: true,
             },
             pre_load: {
                 is_preloading: false,
                 num_images: 0,
                 img_load_cnt: 0,
             },
             // Default transition to use
             storeRouterTransition: 'fade',
 
             // Toggle for main navigation
             isMenuOpen: false,
         }
     },
     computed: {
       isHomeRoute() {
         return this.$route.name == 'Home';
       },
       hasSongList() {
         return this.store_audio.song_list && this.store_audio.song_list.length > 0;
       },
       appClasses() {
         return 'route-name-' + this.$route.name;
       },
       cartTotalQuantity() {
         return this.$store.state.cart.totalQuantity;
       },
       hasItemsInCart() {
         return this.cartTotalQuantity > 0;
       },
 
       isMenuLoading() {
           return this.$store.state.menuLoading;
       },
       menu() {
           return this.$store.state.menu;
       },
       isMobileMenuLoading() {
           return this.$store.state.mobileMenuLoading;
       },
       mobileMenu() {
           return this.$store.state.mobileMenu;
       },
       store() {
           return this.$store.state.store;
       },
       user() {
           return this.$store.state.user;
       },
     },
     created() {
       console.log('Store App Created');
     },
     mounted() {
       console.log('Store App Mounted');
 
       // Load the Store Menu
       this.loadMenu();
 
       // Load the Store Mobile Menu
       this.loadMobileMenu();

       // Preload images
       this.preLoadStoreImages();
 
       // load cart quantity
       this.$store.dispatch('getCartTotalQuantity', this.store.id);
     
       /**  Disable audio fetching can turn this back on if we want mp3s
       this.$api.getStoreAudio(this.store.id).then((song_list) => {
            this.store_audio.song_list = song_list
       });*/
     },
     methods: {
        isMobile() {
            if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                console.log("TRUE MOBILE");
                return true
            } else {
                console.log("FALSE MOBILE");
                return false
            }
        },
         async preLoadStoreImages() {
             this.pre_load.is_preloading = true;
             // Build a list of images to preload
             let image_list = [];
 
            // If this is desktop
             if(!this.isMobile()) {
                 console.log("LOADING HOME PAGE AND BACKGROUND IMAGE");
                // TODO: figure out how to make sure the "home page image is first"
                // Add the HOME page first
                const home_page = this.store.pages.find(page => page.slug == '/');
                if(home_page && home_page.background_image_url) {
                    image_list.push(home_page.background_image_url);
                }

                // Store Page background image
                this.store.pages.forEach(page => {
                    if(page.background_image) {
                        image_list.push(page.background_image_url);
                    }
                });
 
                // Product Categories
                this.store.product_categories.forEach(category => {
                    if(category.background_image) {
                        image_list.push(category.background_image_url);
                    }
                });
 
                // Products
                this.store.products.forEach(product => {
                    if(product.thumbnail) {
                        image_list.push(product.thumbnail_url);
                    }
                    if(product.thumbnail_hover) {
                        image_list.push(product.thumbnail_hover_url);
                    }
                });
             }
 
             // Dedupe the image array
             image_list = uniq(image_list);
 
             // Preload the images
             if(image_list.length > 0) {
                 this.preLoadImages(image_list)
             } else {
                 this.pre_load.is_preloading = false;
             }
 
         },
         async preLoadImages(images) {
             this.pre_load.num_images = images.length;
 
             images.forEach(img_url => {
                 let img = new Image();
                 img.onload = this.imageLoaded;
                 img.onerror = this.imageLoadError;
                 img.src = img_url;
             });
         },
         async loadMenu() {
             this.errors = [];
             console.log('loadMenu()');
             try {
                 this.$store.dispatch('loadMenu');
             }
             catch(error) {
                 this.error.push('Error Loading Store Menu');
                 console.log(error);
                 if(error.response) {
                     this.errors.push(error.response.data.message);
                 }
                 this.$toast.open({
                     message: 'Error Loading Store Menu',
                     type: 'error',
                     duration: 10000
                 });
             }
         },
         async loadMobileMenu() {
             this.errors = [];
             console.log('loadMobileMenu()');
             try {
                 this.$store.dispatch('loadMobileMenu');
             }
             catch(error) {
                 this.error.push('Error Loading Store Mobile Menu');
                 console.log(error);
                 if(error.response) {
                     this.errors.push(error.response.data.message);
                 }
                 this.$toast.open({
                     message: 'Error Loading Store Mobile Menu',
                     type: 'error',
                     duration: 10000
                 });
             }
         },
         imageLoaded() {
             // Callback after an image is loaded
             this.pre_load.img_load_cnt++;
             //console.log("Image Loaded: num_loaded - " + this.pre_load.img_load_cnt)
             if(this.pre_load.img_load_cnt >= this.pre_load.num_images) {
                 this.pre_load.is_preloading = false;
             }
         },
         imageLoadError() {
             // Error Loading image
             this.pre_load.img_load_cnt++;
             console.log('Error Loading Image');
             if(this.pre_load.img_load_cnt >= this.pre_load.num_images) {
                 this.pre_load.is_preloading = false;
             }
         },
         async logout() {
             console.log('logout click');
             try {
                 const response = await axios.post('/logout');
                 window.location.href= "/login";
             }
             catch(error) {
                 console.log('Error Logging Out')
                 console.log(error);
                 this.$toast.error('Error Logging Out');
             }
             
         },
         toggleMenu() {
             this.isMenuOpen = !this.isMenuOpen;
         },
         onMenuClose() {
            this.isMenuOpen = false;
         }
     },
     watch: {
         '$route' (to, from) {
            // Lets disable the main transisiton when going between 
            // category and product pages
             console.log( 'main-router', to.name, from.name);
             if(to.name == 'ProductListing' && from.name == 'ProductDetail' ||
                to.name == 'ProductDetail' && from.name == 'ProductListing') {
                this.storeRouterTransition = '';
                console.log('disable transition')

            } else {
                            this.storeRouterTransition = 'fade';
            }	
        }	
    }	
  }
</script>