<template>
  <div class="product-list-component sidebar-scroll-container" v-if="current_category">
    <header class="product-list-header">
      <div class="product-list-title">
        <span class="current-category-name-shadow">{{current_category.name}}</span>
        <h1 class="current-category-name">{{current_category.name}}</h1>
        <div class="search-header-desktop">
          <input v-model="searchTerm" type="text" :placeholder="'Search ' + [[ current_category.name ]] + '...'" name="search">
        </div>
        <div class="search-header-mobile">
          <input v-model="searchTerm" type="text" :placeholder="'Search ' + [[ current_category.name ]] + '...'" name="search">
        </div>
      </div>
      <div v-if="have_category_choices"
        class="category-choices-field">
        <div class="select-wrapper" @click="toggleCategoryFilter" :class="{'active': isCategoryActive}">
          <div class="currently-selected">
            <div class="selected-bg">
            </div>
            <div class="selected-text">Switch Category:</div>
          </div>
          <div class="selectables">
            <a v-for="category in category_choices"
              :class="{'active-category': isCategorySelected(category)}"
              :key="category.id" :value="category.id" @click="onCategoryChanged(category.id)">
              {{category.name}}
            </a>
          </div>
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
</template>

<script>
  import maxBy from 'lodash/maxBy';
  import minBy from 'lodash/minBy';

  import HoverImage from "./HoverImage.vue";

export default {
  name: "ProductListing",
  components: {
      HoverImage,
  },
  props: {
    products: {
      type: Array,
      default: []
    },
    current_category: {
        type: Object,
        default: null
    },
    category_choices: {
        type: Array,
        default: []
    }
  },
  data() {
    return {
      searchTerm: '',
      category_id: null,
      isCategoryActive: false,
    }
  },
  methods: {
    toggleCategoryFilter: function() {
      this.isCategoryActive = !this.isCategoryActive;
    },
    productClicked(product) {
      this.$emit('productClicked', product.id);
    },
    onCategoryChanged(id) {
      if(this.current_category.id !== id) {
        this.$emit('categoryChanged',  id);
      }
    },
    onSelectedCategoryChange(event) {
        this.$emit('categoryChanged',  event.target.value);
    },
    isCategorySelected(categroy) {
        return this.current_category.id == categroy.id;
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
    sortedProducts() {
      let st = this.searchTerm;
      return this.products.filter(function f(product) {
          if(st == '') return true;
          return (product.name && product.name.toLowerCase().includes(st.toLowerCase()))
          || product.price.includes(st) ||
          (product.sizes && (product.sizes.filter(f)).length);
      }).sort(function(a, b) {
          return a.pivot.display_order - b.pivot.display_order;
      });
    },
    have_category_choices() {
      return this.category_choices.length > 0;
    },
    storeName() {
      return this.$store.state.store.name;
    }
  },
  created() {
       console.log('Product Listing created');
       this.category_id = Number.parseInt(this.$route.params.category_id);

       
  },
  mounted() {
      console.log('Product Listing mounted');
  },
  beforeRouteUpdate(to, from, next) {
    console.log('Product Listing before route update');
    next();
  },
  watch: {
      current_category(newCategory, oldCategory) {
          if(newCategory) {
              // Set document title category name and store name
              document.title = newCategory.name + ' - ' + this.storeName;
          }
      }
  }
}
</script>

<style scoped lang="scss">
</style>
