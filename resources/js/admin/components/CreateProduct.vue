<template>
    <div class="create-product">

        <errors v-bind:errors="errors" style="margin-top:0;"></errors>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <!-- Product Name -->
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="product_name"
                            name="product_name"
                            v-model="name"
                            aria-describedby="product_name"
                            placeholder="Product Name"
                            required
                        />
                    </div>


                    <!-- Product Description -->
                    <div class="form-group">
                        <label for="description">Product Description</label>
                        <textarea
                            class="form-control"
                            id="description"
                            name="description"
                            rows="4"
                            v-model="description"
                            aria-describedby="description"
                            placeholder="This is a long product description."
                            required
                            ></textarea>
                        <small class="form-text text-muted">
                            Product Description can use HTML for formatting.
                        </small>
                    </div>


                    <!-- Price and Shopify Variant -->
                    <div class="form-row">
                        <!-- Product Price -->
                        <div class="col-sm form-group">
                            <label for="product_price">Price</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number"
                                    class="form-control"
                                    id="product_price"
                                    name="product_price"
                                    v-model="price"
                                    aria-describedby="product_price"
                                    placeholder="5.00"
                                    required
                                />

                            </div>
                        </div>

                        <!-- Shopify Variant ID -->
                        <div class="col-sm form-group">
                            <label for="shopify_variant_id">Shopify Variant ID</label>
                            <input
                            type="text"
                            class="form-control"
                            id="shopify_variant_id" 
                            v-model="shopify_variant_id"
                            />
                            <small class="form-text text-muted">
                                All Products (or their Sizes) must have a Shopify Variant ID.
                                This value is obtained from a Shopify Data Export
                            </small>
                        </div>
                    </div>
                </div>
                <!-- end card -->

                <!-- Media -->
                <div class="card">
                    <h5 class="card-title">Media</h5>
                    <!-- product thumbnail and hover -->
                    <div class="row mb-4 border bg-light px-2 py-3">
                        <div class="col-md-6">
                            <div class="form-group">                
                                <label for="thumbnail">Thumbnail</label>
                                <input type="file" id="thumbnail"
                                    @change="onThumbnailChange"
                                    class="form-control-file"/>
                                <small class="form-text text-muted">
                                Image for listing page
                            </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">                
                                <label for="thumbnail_hover">Thumbnail Hover</label>
                                <input type="file" id="thumbnail_hover"
                                    @change="onThumbnailHoverChange"
                                    class="form-control-file"/>
                                <small class="form-text text-muted">
                                    Optional Hover Image for listing page
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="row border bg-light px-2 py-3">
                        <div class="col">
                            <div class="form-group">
                                <label for="add-images">Product Images</label>
                                <input 
                                    class="form-control-file mb-3"
                                    type="file" name="images" id="add-images" ref="images" multiple />
                                <small class="form-text text-muted mb-3">
                                    These are the images shown on the product detail page.<br/>
                                    You may upload multiple files by Ctrl/Cmd clicking filenames<br/> in the upload dialog that appears after clicking 'Choose Files'.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end product media -->
            </div> 
            <!-- end main colum col-->

            <!-- sidebar -->
            <div class="col">
                <div class="card">
                    <!-- Publish Flag -->
                    <div class="form-check published-check">
                        <input type="checkbox" class="form-check-input" id="is_published"
                            v-model="is_published">
                        <label class="form-check-label" for="is_published">Is Published</label>
                        <small class="form-text text-muted">
                            Controls if a product is visible in the store. 
                        </small>
                    </div>
                    
                    <hr/>

                    <!-- Product Category -->
                    <div class="form-group">
                        <label for="name">Category</label>
                        <div class="num-selected">
                            {{selectedProductsCount}} selected
                        </div>
                        <span v-for="cat in product_categories" class="category" :key="cat.id">
                            <div class="form-check published-check">
                                <input type="checkbox" class="form-check-input"
                                    id="categories"
                                    name="categories"
                                    v-model="categories" :value="cat.id">
                                <label class="form-check-label" for="is_published">
                                    <label v-if="!cat.store_page_id">
                                        [M]
                                    </label>
                                    <label v-else>
                                        [D]
                                    </label>
                                    {{cat.name}}
                                </label>
                            </div>
                            <!-- <span v-if="idx < (product.categories.length - 1)" class="comma">, </span> -->
                        </span>
                        <!-- <select
                            class="form-control"

                            id="categories"
                            name="categories"
                            v-model="categories"
                            required>
                        <option value="" selected disabled>-- Select a Category --</option>
                        <option
                            v-for="category in product_categories"
                            :key="category.id"
                            :value="category.id"
                        >{{ category.name }}</option>
                        </select> -->
                        {{categories}}
                    </div>

                    <hr/>
                    
                    <!-- Cart Purcahse Limit -->
                    <div class="form-group">
                        <label for="max_cart_quantity">Maximum Cart Quantity</label>

                        <input type="number" min="1" step="1"
                            class="form-control"
                            id="max_cart_quantity"
                            v-model="max_cart_quantity"
                            placeholder="Unlimited" />
                        <small class="form-text text-muted">
                            Limits the number of items a customer may add to their cart per purchase. 
                            <em>A blank value means there is no limit.</em>
                        </small>
                    </div>

                    <hr/>

                    <!-- Not For Sale Flag -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="not_for_sale"
                            v-model="not_for_sale">
                        <label class="form-check-label" for="not_for_sale">Not For Sale</label>
                        <small class="form-text text-muted">
                            Used for products that are unavailable for purchase, but you still want to show
                            up in the store. 
                        </small>
                    </div>

                    <hr/>

                    <!-- Add to suggested products -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_suggested"
                            v-model="is_suggested">
                        <label class="form-check-label" for="is_suggested">Is Suggested Product</label>
                        <small class="form-text text-muted">
                            Used to add products in suggested products in cart.
                        </small>
                    </div>
                </div>
            </div>
        </div> <!-- end top product row -->

        

          

<!-------------
          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="ext_purchase_url">External Purchase Link</label>
                <input 
                  type="url"
                  class="form-control"
                  id="ext_purchase_url" 
                  name="ext_purchase_url"
                  v-model="ext_purchase_url"
                  aria-describedby="external purchase link"
                  placeholder="https://www.example.com/proudct-url/" />
                <small id="ext_purchase_url_help" class="form-text text-muted">
                  Optional - URL to purchase product off-site. Disables on-site purchase of the product.
                </small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="minimum_age">Minumum Age (Leave blank for no restriction)</label>
                <input
                  type="text"
                  class="form-control"
                  id="minimum_age"
                  name="minimum_age"
                  v-model="minimum_age"
                  aria-describedby="minimum_age"
                />
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="current_inventory">Current Inventory (Leave blank for unlimited)</label>
                <input
                  type="text"
                  class="form-control"
                  id="current_inventory"
                  name="current_inventory"
                  v-model="current_inventory"
                  aria-describedby="current_inventory"
                />
              </div>
            </div>
          </div>
  ---------->       
          
          <!-- Start Product Sizes -->
          <div class="card">              
                <h5 class="card-title">Product Sizes</h5>

                <div v-for="(size, i) in sizes" class="form-row size-row" :key="i">
                    <div class="col-lg-2">
                        <label>Size</label>
                        <input
                        type="text"
                        class="form-control"
                        :id="'size_' + i + '_size'"
                        :name="'size_' + i + '_size'"
                        v-model="size.size"
                        placeholder="XXL"
                        required
                        />
                    </div>

                    <div class="col-lg-2">
                        <label>Price</label>
                        <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input
                            type="number"
                            class="form-control"
                            :id="'size_' + i + '_price'"
                            :name="'size_' + i + '_price'"
                            v-model="size.price"
                            placeholder="3.33"
                            required
                        />
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <label>Shopify Variant ID</label>
                        <input
                        type="text"
                        class="form-control"
                        :id="'size_' + i + '_shopify_variant_id'"
                        :name="'product_edit_size_' + i + '_shopify_variant_id'"
                        v-model="size.shopify_variant_id"
                        />
                        <small class="form-text text-muted">
                        Shopify Variant ID for this size.

                        </small>
                    </div>

                    <div class="col-lg-2">
                        <label>Display Order</label>
                        <input
                        type="number"
                        class="form-control"
                        :id="'size_' + i + '_display_order'"
                        :name="'product_edit_size_' + i + '_dispaly_order'"
                        v-model="size.display_order"
                        required
                        />
                    </div>

                    <div class="col-lg-1">
                        <label>Is Available</label>
                        <input
                            type="checkbox"
                            class="form-control"
                            :id="'size_' + i + '_is_available'"
                            :name="'product_edit_size_' + i + '_is_available'"
                            v-model="size.is_available"
                            required
                        />
                    </div>

                    <div class="col-lg-2 col-delete">
                        <button type="button" 
                                name="button" 
                                v-on:click="removeSizeOption(i)" class="btn btn-danger">Delete</button>
                    </div>
            </div>
              

              <!-- Add Size Button Row -->
                <div class="row pt-2 mt-2">
                  <div class="col">
                    <button
                      type="button"
                      class="btn btn-secondary"
                      @click="addSizeOption()"
                    >+ Add Product Size</button>
                  </div>
              </div>

          </div>

        <div class="btn-toolbar mt-2 pt-2">
            <div class="btn-group mr-4">
                <button type="submit" class="btn btn-primary" @click.prevent="createProduct">Save</button>
            </div>
            <div class="btn-group mr-auto">
                <slot name="cancel-link"></slot>
            </div>
        </div>
      
    </div>
</template>

<script>
import AudioPlayer from '../../frontend/components/AudioPlayer.vue';
export default {
  components: { AudioPlayer },
  props: {
    store_id: Number,
    saveUrl: String,
    successUrl: String,
  },
  data() {
    return {
      name: "",
      shopify_variant_id: null,
      description: "",
      price: null,
      is_published: false,
      is_suggested: false,
      max_cart_quantity: null,
      minimum_age: null,
      current_inventory: null,
      ext_purchase_url: null,
      price: null,
      not_for_sale: false,
      categories: [],
      sizes: [],
      newThumbnail: null,
      newThumbnailHover: null,
      product_categories: product_categories,
      errors: [],

    };
  },
    methods: {
        async createProduct() {
          console.log("CREATE PRODUCT");

            this.errors = [];
            var formData = new FormData();

            ["name", "shopify_variant_id", "description", "price",
                "is_published", "max_cart_quantity",
                "current_inventory", "minimum_age", "ext_purchase_url",
                "not_for_sale"].forEach((dataKey) => {
                    if(this[dataKey]) formData.append(dataKey, this[dataKey]);
            });

            // Add categories
            if(this.categories) {
                formData.append("categories", JSON.stringify(this.categories));
            }

            // Add Thumbnail and Hover Images if needed
            if(this.newThumbnail) {
                formData.append("thumbnail", this.newThumbnail);
            }
            if(this.newThumbnailHover) {
                formData.append("thumbnail_hover", this.newThumbnailHover);
            }

            formData.append("sizes", JSON.stringify(this.sizes));
            var ins = this.$refs.images.files.length;

            for (var x = 0; x < ins; x++) {
                formData.append("images[]", this.$refs.images.files[x]);
            }
            try {
                const response = await axios.post(this.saveUrl, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                });
                console.log(response);
                if(response.data.success) {
                  this.$toast.success('Product Created');
                    window.location.href = this.successUrl;
                } else {
                     this.$toast.error('Unknown Error Saving Product');
                }
            }
            catch(error) {
                console.log('Error Saving Product', error);
                const err_msg_prefix = 'Error Saving Product';
                let err_msg = err_msg_prefix;

                if(error.response && error.response.status == 413){
                    err_msg += 'Product Images should be smaller than 5MB';
                }
                if( error.response && error.response.data.message) {
                    err_msg += ' - ' + error.response.data.message;
                } else if(error.message) {
                    err_msg += ' - ' + error.message;
                }
                
                this.errors.push(err_msg);
                
                if(error.response && error.response.data.errors) {
                    for( const prop in error.response.data.errors) {
                        this.errors.push(error.response.data.errors[prop][0]);
                    }
                }

                // Show a toast message and scroll back to error
                this.$toast.error(err_msg_prefix);
                window.scroll(0,0);
            }

        },
        addSizeOption: function() {
            this.sizes.push({ size: null, price: null, shopify_variant_id: null, display_order: 10, is_available: true });
        },
        removeSizeOption: function(i) {
            this.sizes.splice(i, 1);
        },
        onThumbnailChange(event) {
            console.log('Thumbnail Change');
            this.newThumbnail = this.getFile(event);
        },
        onThumbnailHoverChange(event) {
            console.log('Thumbnail Hover Change');
            this.newThumbnailHover = this.getFile(event);
        },
        getFile(event) {
            let files = event.target.files;
            if(files.length > 0 ) {
                return files[0];
            } else {
                return null;
            }
        }
    },

    computed: {
      selectedProductsCount() {
          if (this.categories) {
              return this.categories.length;
          }
          return null;
      }
    },
};
</script>

<style lang="scss" scoped>
  .page-title {
    small {
      display: block;
      font-size: 12px;
      text-transform: uppercase;
    }
  }
</style>