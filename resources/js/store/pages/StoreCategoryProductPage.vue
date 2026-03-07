<template>
	<div class="store store-category-page" :class="[pageClasses]">
		<div
			class="page-content-container"
			:style="{ backgroundImage: `url(${pageBackgroundImage})` }">

			<div class="page-content-inner">
				<div class="store-sidebar">
					<div class="sidebar-container">
						<div class="sidebar-content">

							<header class="sidebar-header" v-if="category">
								<router-link
									class="back-to-store"
									v-if="hasParentStorePage"
									:to="{
										name: 'Page',
										params: { slug: category.store_page.slug },
									}">
									<span>To {{ category.store_page.name }}</span>
								</router-link>

								<router-link v-if="category_id"
									:to="{
										name: 'ProductListing',
										params: { category_id: category.id },
									}"
									class="back-to-category desktop-category" >
									&#60;&#60; Back to <span>{{ category.name }}</span>
								</router-link>
							</header>

							<header class="sidebar-header" v-if="mobile_category">
								<router-link v-if="mobile_category_id"
									:to="{
										name: 'ProductListing',
										params: { category_id: mobile_category.id },
									}"
									class="back-to-category mobile-category" >
									&#60;&#60; Back to <span>{{ mobile_category.name }}</span>
								</router-link>
							</header>

							<header v-if="!category && !mobile_category" class="sidebar-header no-category-header">
							</header>

							<div class="router-view-wrapper sidebar-body">
								<transition name="fade" mode="in-out" appear>
									<router-view
										:key="$route.fullPath"
										:current_category="category"
										:products="productsForCategory"
										:category_choices="category_choices"
										@productClicked="productClicked"
										@categoryChanged="onCategoryChange">
									</router-view>
								</transition>
							</div>

						</div>
					</div>
				</div>

			</div>
		</div>
		<portal to="home-button">
			<!-- if you want to go to the parent page change to :to="category_store_page.slug" -->
			<router-link v-if="category && category.store_page && category.store_page.parent"
				class="btn btn-main-menu btn-text"
				:to="parentPageRoute">
				Back <span>to {{category.store_page.parent.name}}</span>
			</router-link>
		</portal>
	</div>
</template>

<script>
import ProductList from "../components/ProductListing.vue";

export default {
	name: "StoreCategoryProductPage",

	data() {
		return {
			store: window.__INIT_STATE__.store,
			category_id: null,
			mobile_category_id: null,
			product_id: null,
			category_choices: [],
			category: null,
			mobile_category: null,
			errors: [],
			product: null,
		};
	},
	components: {
		ProductList,
	},
	created() {
		console.log("StoreCategoryProductPage Created");
		console.log("params: ", this.$route.params);
		this.category_id = this.$route.params.category_id;
		this.product_id = this.$route.params.product_id;
		this.loadData();
	},
	mounted() {
	},
	beforeRouteUpdate(to, from, next) {
		console.log("--> StoreCategoryProductPage beforeRouteUpdate: ");
		console.log(to);
		console.log(from);
		if (to.name == "ProductListing") {
			console.log("beforeRouteUpdate() to ProductListing");
			console.log(to);
			console.log(from);
			this.category_id = to.params.category_id;
			this.loadData();
		} else if (to.name == "ProductDetail") {
			console.log("beforeRouteUpdate() to ProductDetail");
			this.product_id = to.params.product_id
			this.loadData()
		}
		next(); // DO NOT DELETE
	},
	methods: {
		productWithoutStorePage(categories) {
			for (var i=0; i < categories.length; i++) {
				if (!categories[i].store_page_id) {
					return categories[i].id;
				}
			}
		},
		productWithStorePage(categories) {
			for (var i=0; i < categories.length; i++) {
				if (categories[i].store_page_id) {
					return categories[i].id;
				}
			}
		},
		async loadData() {
			console.log("loadData() - Start - " + this.category_id);
			if(!this.category_id && this.product_id) {
				this.product = this.$store.getters.getProductByID(this.product_id);
				if( !this.product ) {
					this.product = await this.$store.dispatch('loadProduct', this.product_id);
				}
				if( this.product.categories.length > 0 ) {
					this.mobile_category_id = this.productWithoutStorePage(this.product.categories);
					this.category_id = this.productWithStorePage(this.product.categories);
				}
			}
			if( this.category_id ) {
				this.category = await this.loadCategory(this.category_id);
				if(this.category.store_page) {
					this.category_choices = await this.loadCategoryChoicesForPage(
						this.category.store_page.slug
					);
				}
			}
			if( this.mobile_category_id ) {
				this.mobile_category = await this.loadCategory(this.mobile_category_id);
			}
		},
		async loadCategory(categoryID) {
			console.log("loadCategory:" + categoryID);
			let category = this.$store.getters.getCategoryByID(categoryID);
			if( !category ) {
				category = await this.$store.dispatch('loadCategory', categoryID);
			}
			return category;
		},
		async loadCategoryChoicesForPage(pageSlug) {
			console.log("loadCategoryChoicesForPage: ", pageSlug);
			let page = this.$store.getters.getPageBySlug(pageSlug);
			if( !page ) {
				page = await this.$store.dispatch('loadPageBySlug', pageSlug);
			}
			return page.categories;
		},
		productClicked(productId) {
			console.log("caught emittied productClicked - " + productId);
			this.$router.push({
				name: "ProductDetail",
				params: { product_id: productId },
			});
		},
		// Category Select Filter
		onCategoryChange(new_category) {
			console.log("onCategoryChange");
			console.log(new_category);

			// Show other category
			this.$router.push({
				name: "ProductListing",
				params: { category_id: new_category },
			});
		},
	},
	computed: {
		pageBackgroundImage() {
			// Use the category's background image but use
			// the category's parent page background image as a fallback
			if( this.category ) {
				if( this.category.background_image ) {
					return this.category.background_image_url;
				} else if( this.category.store_page && this.category.store_page.background_image) {
					return this.category.store_page.background_image_url;
				}
			} else {
				return "";
			}
		},
		have_cateogry_choices() {
			return this.category_choices.length > 0;
		},
		isProductDetail() {
			return this.$route.name == "ProductDetail";
		},
		isProductListing() {
			return this.$route.name == "ProductListing";
		},
		hasParentStorePage() {
			return this.category && this.category.store_page;
		},
		pageClasses() {
			return {
				"is-product-listing-view": this.isProductListing,
				"is-product-detail-view": this.isProductDetail,
			};
		},
		productsForCategory() {
			if(this.category) {
				return this.category.products;
			} else {
				return []
			}
		},
		parentPageRoute() {
			if( this.category.store_page.parent.slug == '/' ||
			    this.category.store_page.parent.slug == '' ) {
					return {
						name: 'Home'
					}
			} else {
				return {
					name: 'Page',
					params: { slug: this.category.store_page.parent.slug },
				};
			}
		}
	},
};
</script>

<style scoped lang="scss">
</style>