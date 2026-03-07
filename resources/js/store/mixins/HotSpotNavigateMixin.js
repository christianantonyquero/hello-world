export default {
    methods: {
        navigate(hotspot) {
            console.log('HotSpotNavigateMixin -- navigate()', hotspot);
            // Navigate to the hotspot's destination
            if (hotspot.type == "PRODUCT") {
                this.nonRedundentPush({
                    name: 'ProductDetail',
                    params: {
                        product_id: hotspot.value
                    }
                });
                return;
            }
            if (hotspot.type == "PRODUCT_CATEGORY") {
                this.nonRedundentPush({
                    name: 'ProductListing',
                    params: {
                        category_id: hotspot.value
                    }
                });
                return;

            }
            if (hotspot.type == "PAGE") {
                // slug's with / or blank may cause issue so send them to Home
                if (hotspot.value == '/' || hotspot.value == '') {
                    this.nonRedundentPush({
                        name: 'Home'
                    });
                } else {
                    this.nonRedundentPush({
                        name: 'Page',
                        params: {
                            slug: hotspot.value
                        }
                    });
                }
                return;
            }
            if (hotspot.type == "HOME") {
                this.nonRedundentPush({
                    name: 'Home'
                });
                return;
            }
            if (hotspot.type == 'URL') {
                window.open(hotspot.value, "_blank");
                return;
            }
            if (hotspot.type == 'PHOTO-BOOTH') {
                this.nonRedundentPush({
                    name: 'PhotoBooth'
                });
                return;
            }

            // Fallback route is the home page
            this.nonRedundentPush({
                name: 'Home'
            });
            return;
        },
        navigateCategory(category) {
            this.nonRedundentPush({
                name: 'ProductListing',
                params: {
                    category_id: category.id
                }
            });
            return;
        },
        // A push method that prevents redundent route errors
        nonRedundentPush(params) {
            let from = this.$route.fullPath;
            let to = this.$router.resolve(params).route.fullPath;
            //console.log('nonReduentPush', from, to);
            if( from === to ) {
                // redundent navigation, don't to it
                return;
            }
            this.$router.push(params);

        }
    }
}