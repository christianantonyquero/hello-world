<template>
	<div
		v-if="product"
		class="product product-detail sidebar-scroll-container"
		:class="{ 'product-external-purchase': product.ext_purchase_url }"
	>
		<header class="product-header">
		</header>
		<div class="sidebar-scroll">
			<div class="columns is-multiline">
				<div class="column is-full is-half-fullhd">
					<product-image-gallery :images="product.images"></product-image-gallery>
				</div>
				<div class="column is-full is-half-fullhd">

					<div class="product-body">
						<h1 class="name">{{ product.name }}</h1>
						<div class="description">
							<div class="description-wrapper" v-html="product.description"></div>
						</div>
					</div>

					<footer class="product-footer">
						<template v-if="!product.not_for_sale">
							<template v-if="product.ext_purchase_url">
								<div class="row">
									<div class="col add-cart-wrapper">
										<a
											class="button is-primary is-large"
											:href="product.ext_purchase_url"
											target="_blank"
										>
											Purchase Off Site
										</a>
									</div>
								</div>
							</template>
							<template v-else>
								<div class="product-selection">
									<div class="form-group quantity">
										<label for="id_quantity">Quantity</label>
										<div class="increment-decrement">
											<a class="decrement" @click="decrementQuantity">-</a>
											<input
												type="text"
												required
												id="id_quantity"
												class="form-control"
												v-model="quantity"
											/>
											<a class="increment" @click="incrementQuantity">+</a>
										</div>
									</div>
									<div v-if="has_sizes" class="form-group size">
										<label for="id_size">Size</label>
										<div
											class="select-wrapper small drop-up"
											@click="toggleSizeFilter"
											:class="{ active: isSizeActive }"
										>
											<div class="currently-selected">
												<div class="selected-bg"></div>
												<div v-if="!selected_size.size" class="selected-text">
													Select A Size
												</div>
												<div v-if="selected_size.size" class="selected-text">
													{{ selected_size.size }}
												</div>
											</div>
											<div class="selectables">
												<a
													v-for="size in product.sizes"
													:key="size.id"
													:value="size"
													@click="selectSize(size)"
												>
													{{ size.size }}
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="">
									<div class="add-cart-wrapper buy-now">
										<button
											class="button is-primary-dark is-medium"
											:disabled="!can_add_to_cart"
											@click="buyNow"
										>
											Buy Now
										</button>
									</div>
								</div>
								<div class="">
									<div class="add-cart-wrapper">
										<button
											class="button is-primary-dark is-medium"
											:disabled="!can_add_to_cart"
											@click="addToCart"
										>
											Add To Cart
										</button>
										<span v-if="price" class="price">{{ price | currency }}</span>
									</div>
								</div>
							</template>
						</template>
					</footer>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import ProductImageGallery from "./ProductImageGallery.vue";

export default {
	name: "ProductDetail",
	components: {
		ProductImageGallery,
	},
	props: {
		// Hacky way to get the parent category name, the parent router
		// pass the current category to the router-view as a prop
		current_category: {
			type: Object,
			default: null,
		},
	},
	data() {
		return {
			isSizeActive: false,
			product_id: null,
			product: null,
			store: window.__INIT_STATE__.store,
			quantity: 1,
			selected_size: "",
		};
	},
	created() {
		console.log("Product Detail - created()");
		this.product_id = Number.parseInt(this.$route.params.product_id);
		this.loadProduct(this.product_id);
	},
	beforeRouteUpdate(to, from, next) {
		next();
	},
	computed: {
		has_sizes() {
			return this.product.sizes.length > 0;
		},
		price() {
			// The product's price depends on if the proudct has sizes and if a size is selected
			if (this.has_sizes) {
				if (this.selected_size) {
					return this.selected_size.price;
				} else {
					return null;
				}
			} else {
				return this.product.price;
			}
		},
		can_add_to_cart() {
			// Show cart button if we have price and quantity
			if (this.price && this.quantity) {
				return true;
			} else {
				return false;
			}
		},
		storeName() {
			return this.$store.state.store.name;
		},
	},
	methods: {
		decrementQuantity: function () {
			if (this.quantity > 0) {
				this.quantity = this.quantity - 1;
			}
		},
		incrementQuantity: function () {
			this.quantity = this.quantity + 1;
		},
		toggleSizeFilter: function () {
			this.isSizeActive = !this.isSizeActive;
		},
		async loadProduct(productID) {
			console.log("Loading Product: " + productID);
			try {
				
				this.product = this.$store.getters.getProductByID(productID);
				if( !this.product ) {
					this.product = await this.$store.dispatch('loadProduct', productID);
				}
				if(this.product && this.product.sizes.length > 0) {
					this.selectSize(this.product.sizes[0]);
				}
				// Set document title category name and store name
				document.title = this.product.name + " - " + this.storeName;

				// Track Product View
				this.$gtag.gaViewProduct([
					{
						name: this.product.name,
						currency: "USD",
					},
				]);
			}
			catch(error) {
				console.log('Error Loading Product');
				console.log(error);
				this.$toast.error('Error Loading Product');

			}
		},
		selectSize(size) {
			this.selected_size = size;
		},
		async buyNow() {
			this.addToCart().then(
			setTimeout( () => this.$router.push({ name: 'Cart',
					params: { is_auto_checkout: true } }), 2000)
			);
		},
		async addToCart() {
			try {
				console.log("Adding product to cart", this.product.id);
				const response = await this.$api.addProductToCart(
					this.store.id,
					this.product.id,
					this.quantity,
					this.selected_size ? this.selected_size.id : null
				);
				this.$toast.success("Item added to cart!");
				// Update cart total
				this.$store.dispatch("getCartTotalQuantity", this.store.id);

				// Track add to Cart
				this.$gtag.gaAddToCart([
					{
						name: this.product.name,
						quantity: this.quantity,
						currency: "USD",
					},
				]);
			} catch (error) {
				this.$toast.error(error.response.data.message);
			}
		},
	},
};
</script>

<style scoped lang="scss">
</style>
