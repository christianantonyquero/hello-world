<template>
    <div class="store cart-container is-product-listing-view" :style="containerStyles">
        <div class="cart">
            <div class="product-list-component sidebar-scroll-container">
                <header class="product-list-header">
                    <div class="product-list-title">
                        <span class="current-category-name-shadow">All Products</span>
                        <h1 class="current-category-name">All Products</h1>
                        <div class="search-header-desktop">
                        <input v-model="searchTerm" type="text" placeholder="Search All Products..." name="search">
                        </div>
                        <div class="search-header-mobile">
                        <input v-model="searchTerm" type="text" placeholder="Search All Products..." name="search">
                        </div>
                    </div>
                </header>
                <div class="sidebar-scroll">
                    <div class="product-list">
                        <div v-for="product in sortedProducts" :key="product.id"
                        class="product"
                        :class="{'product-external-purchase' : product.ext_purchase_url}">
                        <div class="product-container">
                            <div class="product-link" @click="productClicked(product)">
                            <div class="product-image-border">
                                <span class="product-top"></span>
                                <span class="product-left"></span>
                                <div class="product-image">
                                <hover-image
                                    :imageUrl="product.thumbnail_url"
                                    :hoverUrl="product.thumbnail_hover_url"
                                    :alt="product.name">
                                </hover-image>
                                </div>
                                <h4 class="product-name">{{ product.name }}</h4>
                                <template v-if="!product.not_for_sale && !product.ext_purchase_url">
                                <p class="price">
                                    {{ getProductPrice(product) }}
                                </p>
                                </template>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import maxBy from 'lodash/maxBy';
import minBy from 'lodash/minBy';
import { HollowDotsSpinner } from 'epic-spinners';
import HoverImage from "../components/HoverImage.vue";

export default {
    name: "Products",
    components: {
        HollowDotsSpinner,
        HoverImage,
    },
    data() {
        return {
            searchTerm: '',
            products: null,
            store: window.__INIT_STATE__.store,
        }
    },
	created() {
		console.log("Product Detail - created()");
		this.loadProducts();
	},
    mounted() {
    },
    methods: {
		async loadProducts() {
			try {
				// this.products = this.$store.getters.getProducts();
				if( !this.products ) {
					this.products = await this.$store.dispatch('loadProducts');
				}
                console.log("products", this.products);
				if(this.products && this.products.sizes && this.products.sizes.length > 0) {
					this.selectSize(this.products.sizes[0]);
				}
				// Set document title category name and store name
				document.title = "All Products" + " - " + this.storeName;

				// Track Product View
				this.$gtag.gaViewProducts([
					{
						name: "All Products",
						currency: "USD",
					},
				]);
			}
			catch(error) {
				console.log('Error Loading Products');
				console.log(error);
				this.$toast.error('Error Loading Products');

			}
		},
        productClicked(product) {
            console.log("product.id clicked:", product.id);
            // this.$emit('productClicked', product.id);
            
			this.$router.push({
				name: "ProductDetail",
				params: { product_id: product.id },
			});
        },
        getProductPrice(product) {
            if(product.sizes.length > 1) {
                // multiple size return a range (max and min prices)
                // cast my price as a number to compponsinate for Laravel returning decimals as strings
                let maxPrice = maxBy(product.sizes, (s) => Number(s.price)).price;
                let minPrice = minBy(product.sizes, (s) => Number(s.price)).price;

                if(minPrice == maxPrice) {
                    return this.$options.filters.currency(maxPrice);
                } else {
                    return this.$options.filters.currency(minPrice) + ' - ' +
                            this.$options.filters.currency(maxPrice);
                }

            } else if(product.sizes.length == 1 ) {
                // One size, show it
                return this.$options.filters.currency(product.sizes[0].price);
            } else {
                //No sizes, show product price
                return this.$options.filters.currency(product.price);
            }
        }
    },
    computed: {
        pageBackgroundImage() {
            if( this.store.cart_bg_image ) {
                return this.store.cart_bg_image_url;
            } else {
                return "";
            }
        },
        containerStyles() {
            let styles = {};
            if( this.pageBackgroundImage ) {
                styles['backgroundImage']  = `url(${this.pageBackgroundImage})`;
            }
            return styles;
        },
        sortedProducts() {
            
            if( this.products ) {
                let st = this.searchTerm;
                let prods = this.products;
                console.log("this.products: ", this.products);
                return prods.filter(function f(product) {
                    if(st == '') return true;
                    return (product.name && product.name.toLowerCase().includes(st.toLowerCase()))
                    || product.price.includes(st) ||
                    (product.sizes && (product.sizes.filter(f)).length);
                });
            }
        },
        storeName() {
        return this.$store.state.store.name;
        }
    }
}
</script>

<style scoped lang="scss">
    .alert {
        ul {
            margin-bottom: 0;
        }
    }
    .checkout-footer {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
    }
</style>
