<template>
    <div class="cart-container" :style="containerStyles">
        <div class="cart">
            <div class="cart-header">
                <h2 class="card-title">SHOPPING CART</h2>

                <div v-if="errors.length > 0" class="alert alert-danger mb-4">
                    <h5 class="alert-heading">Checkout Error</h5>
                    <ul>
                        <li v-for="(err, idx) in errors" :key="idx">{{err}}</li>
                    </ul>
                </div>

                <div v-if="shopifyCheckout" class="cart-checkout-success-wrapper">
                    <div class="alert alert-success mb-4">
                        <p>
                            Your checkout page should open in a new window.
                            If you do not see the checkout page, then you can click the "View Checkout" button below.<br/>
                            After your purchase is complete you may <span @click="clearCart" class="clear-cart-link">click  here to clear your cart.</span>
                        </p>
                    </div>
                    <a :href="shopifyCheckout.webUrl" target="_blank"
                        class="button is-primary-dark">
                            View Checkout
                    </a>
                </div>
            </div>

            <div class="cart-body">

                <template v-if="!is_cart_empty">
                    <table class="table cart-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-image"></th>
                                <th class="col-name" scope="col">Product</th>
                                <th class="col-qty" scope="col">Quantity</th>
                                <th class="col-cost text-right" scope="col">Cost</th>
                                <th scope="col" class="col-remove"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cart-item-row" v-for="item in cart.items" :key="item.id">
                                <td class="col-image"><img :src="item.image"></td>
                                <td class="col-name">
                                    <span class="name">{{ item.name }}</span>
                                    <span v-if="item.size" class="size">Size: {{item.size}}</span>
                                </td>
                                <td class="col-qty">
                                  <div class="increment-decrement">
                                    <button type="button"
                                        @click.prevent="decrement(item)"
                                        :disabled="is_checking_out">-</button>
                                        <span class="quantity">{{ item.quantity }}</span>
                                    <button type="button"
                                        @click.prevent="increment(item)"
                                        :disabled="is_checking_out">+</button>
                                  </div>
                                </td>
                                <td class="text-right col-cost item-cost">${{ item.subtotal }}</td>
                                <td class="col-remove">
                                    <button type="button"
                                        class="button is-primary-dark is-outlined"
                                        :disabled="is_checking_out"
                                        @click.prevent="removeItem(item)">
                                        x
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <div class="clear-cart">
                            <button @click="clearCart" type="button" class="button is-white is-small is-outlined">Clear Cart</button>
                        </div>
                        <div class="subtotal">
                            Subtotal
                        </div>
                        <div class="item-cost highlight text-right">
                            $ {{ cart.subtotal }}
                        </div>
                    </div>
                </template>

                <template v-else>
                    <p class="empty-cart">Your cart is currently empty.</p>
                </template>
            </div>

            <div class="checkout-footer field is-grouped">
                <div class="control">
                    <a class="button is-primary-dark is-outlined"
                        @click="cancelClick">
                        Cancel
                    </a>
                </div>
                <div class="control">
                    <hollow-dots-spinner
                        v-if="is_processing"
                        class="processing-icon"
                        :animation-duration="2000"
                        :dot-size="10"
                        :dots-num="3"
                        :color="'#4A6060'"
                    />
                    
                    <a  v-if="can_checkout" 
                        :disabled="!can_checkout"
                        class="button is-primary-dark" 
                        ref="myBtn"
                        @click="checkoutClick"
                        dusk="checkout">
                            Checkout
                    </a>
                </div>
            </div>
            <div v-if="suggested && filteredSuggestedProducts.length" class="product-suggestion">
                <div class="product-suggestion-section-title">
                    <h2>You May Also Like</h2>
                </div>
                <div class="product-suggestion-list">
                    <a v-for="product in filteredSuggestedProducts" :key="product.id" href="#" class="product-suggestion-item">
                        <div class="product-suggestion-item-content" @click="productClicked(product)">
                            <div class="product-suggestion-image">
                                <img :src="product.thumbnail_url" alt="">
                            </div>
                            <div class="product-suggestion-item-details">
                                <h1>{{ product.name }}</h1>
                                <p>{{ getProductPrice(product) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import maxBy from 'lodash/maxBy';
import minBy from 'lodash/minBy';
import axios from 'axios';
import Client from 'shopify-buy';
import { HollowDotsSpinner } from 'epic-spinners';

export default {
    components: {
        HollowDotsSpinner,
    },
    data() {
        return {
            cart: null,
            suggested: null,
            store: window.__INIT_STATE__.store,
            storeId: window.__INIT_STATE__.store.id,
            shopifyClientParams: {
                domain: window.__INIT_STATE__.SHOPIFY_STORE_DOMAIN,
                storefrontAccessToken: window.__INIT_STATE__.SHOPIFY_STOREFRONT_ACCESS_TOKEN,
            },
            shopifyClient: null,
            shopifyCheckout: null,
            is_processing: false,
            errors: [],
            is_auto_checkout: false,
        }
    },
    created() {
        if (this.$route.params.is_auto_checkout) {
            console.log(this.$route.params.is_auto_checkout);
            this.is_auto_checkout = this.$route.params.is_auto_checkout;
        }
    },
    mounted() {
        try {
            this.shopifyClient = Client.buildClient(this.shopifyClientParams);
        }
        catch(error) {
            let err_msg = "Error Init Shopify Client";
            console.log(err_msg);
            console.log(error);
            this.errors.push(err_msg);
            this.$toast.error(err_msg);
        }
        this.loadCart().then(this.loadSuggested()).then(() => {
            if ( this.is_auto_checkout ) {
                this.checkoutClick();
            }
        });
        // Set document title page name and store name
        document.title = 'Shopping Cart - ' + this.store.name;
    },
    methods: {
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
        },
        async loadCart() {
            try {
                const response = await axios.get('/store/' + this.storeId + '/api/cart');
                this.cart = response.data;
                // Update cart quantity
                this.$store.dispatch('getCartTotalQuantity', this.storeId);
            }
            catch(error) {
                console.log('Error Fetching Cart', error);
            }
        },
        async loadSuggested() {
            try {
                const response = await axios.get('/store/' + this.storeId + '/api/cart/suggested');
                this.suggested = response.data;
                console.log("SUGGESTED ITEMS", this.suggested);
            }
            catch(error) {
                console.log('Error Fetching Suggested Products', error);
            }
        },
		productClicked(product) {
			console.log("caught emittied productClicked - " + product.id);
			this.$router.push({
				name: "ProductDetail",
				params: { product_id: product.id },
			});
		},
        async increment(item) {
            try {
                const response=await axios.patch('/store/'+this.storeId+'/api/cart/'+item.id, {
                    'quantity': item.quantity+1,
                });

                this.loadCart(); // reload
            } catch(error) {
                this.$toast.error(error.response.data.message);
            }
        },
        async decrement(item, event) {
            try {
                const response = await axios.patch('/store/'+this.storeId+'/api/cart/'+item.id, {
                    'quantity': item.quantity-1,
                });

                this.loadCart(); // reload
            } catch(error) {
                console.log(error);
                this.$toast.error(error.response.data.message);
            }
        },
        async removeItem(item) {
            try {
                const response=await axios.patch('/store/'+this.storeId+'/api/cart/'+item.id, {
                    'quantity': 0,
                });
                this.loadCart(); // reload
            } catch(error) {
                console.log(error);
                this.$toast.error(error.response.data.message);
            }
        },
        cancelClick() {
            // Go back if we have a history or back to home
            if(window.history.length > 2) {
                this.$router.go(-1);
            } else {
                this.$router.push({ name: 'Home' });
            }
        },
        async checkoutClick() {
            this.clearErrors();

            // Can't checkout bail
            if( !this.can_checkout) {
                return false;
            }

            try {
                // Create a shopify checkout object and add line items to it
                this.shopifyCheckout = null;
                this.is_processing = true;
                const lineItems = this.buildShopifyLineItems();
                this.shopifyCheckout = await this.createShopifyCheckout(lineItems);

                // Track Checkout
                this.$gtag.gaBeginCheckout();

                // Open Shopify URL
                window.open(this.shopifyCheckout.webUrl, "_blank");

                // Poll the shopify checkout status to 'complete the checkout' on our end
                await this.wait(10 * 1000);
                let isCheckoutComplete = await this.isShopifyCheckoutComplete();
                while( !isCheckoutComplete ) {
                    await this.wait(5 * 1000);
                    isCheckoutComplete = await this.isShopifyCheckoutComplete();
                }
                if(isCheckoutComplete) {
                    // Complete the checkout
                    await this.checkoutComplete();
                }
            }
            catch(error) {
                console.error(error, error.message);
                this.errors.push(error.message);
                this.$toast.error(error.message, {
                    duration: 5000
                });
            }
            finally {
                this.is_processing = false;
            }
        },
        encodeShopifyVariantId(variantID) {
            const variantPrefix = 'gid://shopify/ProductVariant/';
            return btoa(`${variantPrefix}${variantID}`);
        },
        buildShopifyLineItems() {
            let lineItems = [];

            this.cart.items.forEach(el => {
                // Check to make sure we have a shopify ID
                if(el.shopify_variant_id === undefined || el.shopify_variant_id === null) {
                    let err_product = 'Name: ' + el.name;
                    if(el.size) {
                        err_product += ' Size: ' + el.size
                    }
                    throw new Error(`Product is Missing Shopify ID - ${err_product}`);
                }

                let item = {};
                item.variantId = this.encodeShopifyVariantId(el.shopify_variant_id);
                item.quantity = parseInt(el.quantity);
                lineItems.push(item);
            });

            return lineItems;
        },
        async createShopifyCheckout(lineItems) {
            let checkout = null;
            let checkoutInput = {};
            checkoutInput.lineItems = lineItems;

            if(this.user && this.user.email) {
                checkoutInput.email = this.user.email;
            }
            try {
                checkout = await this.shopifyClient.checkout.create(checkoutInput);
            }
            catch(error) {
                console.log(error, error.message);
                throw new Error('Error Creating Checkout');
                this.shopifyCheckout = null;
            }
            console.log('Checkout Obj:', checkout);

            // check to make sure the products were added
            if(checkout.lineItems.length == 0) {
                throw new Error('Error Adding Product to Shopify Checkout. No products added.');
            }
            return checkout;
        },
        async clearCart() {
            console.log('Clear Out Shopping Cart');
            try {
                const result = await this.$api.clearCart(this.storeId);
                this.cart = null;
                // Update cart quantity
                this.$store.dispatch('getCartTotalQuantity', this.storeId);
            }
            catch(error) {
                console.log(error);
                throw new Error('Error Clearing Shopping Cart');
            }
        },
        clearErrors() {
            this.errors = [];
        },
        async checkoutComplete() {
            // Checkout complete, clear cart and remove checkout object
            await this.clearCart();
            this.shopifyCheckout = null;
        },
        async wait(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        async isShopifyCheckoutComplete() {
            console.log('isShopifyCheckoutComplete');

            // Make sure we have a checkout object, bail if not
            if(this.shopifyCheckout == null) {
                throw new Error('Invalid Shopify Checkout')
            }

            try {
                console.log(this.shopifyCheckout);
                const checkout = await this.shopifyClient.checkout.fetch(this.shopifyCheckout.id);
                console.log('Checkout Status:', checkout, checkout.id, checkout.completedAt);
                if(checkout.completedAt !== null) {
                    return true;
                }
            }
            catch(error) {
                console.log('Error Fetching Checkout', error);
                throw new Error('Error Fetching Checkout Status');
            }
        },
    },
    computed: {
        filteredSuggestedProducts() {
            let suggestedItems = this.suggested
            // If there is no cart items we don't need to remove anything from suggested products
            if (this.cart){
                // We remove items that is already on the users cart
                let cartItems = this.cart.items;
                suggestedItems = suggestedItems.filter(ad =>
                    cartItems.every(fd => fd.name !== ad.name));
            }
            return suggestedItems;
        },
        // helpers
        is_cart_empty() {
            return this.cart == null || this.cart.items == null || this.cart.items.length == 0;
        },
        have_items_in_cart() {
            return !this.is_cart_empty;
        },
        has_errors() {
            return this.errors.length > 0;
        },
        can_checkout() {
            return this.have_items_in_cart && !this.has_errors && !this.is_processing && !this.shopifyCheckout;
        },
        is_checking_out() {
            return this.shopifyCheckout !== null;
        },
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
        user() {
            return this.$store.state.user;
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
