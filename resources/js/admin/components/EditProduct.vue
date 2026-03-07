<template>
    <div class="edit-product">
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
                            id="product_edit_name"
                            name="product_edit_name"
                            v-model="product.name"
                            aria-describedby="product_name"
                            placeholder="Product Name"
                            required/>
                    </div>

                    <!-- product description -->
                    <div class="form-group">
                        <label for="name">Product Description</label>
                        <textarea
                            class="form-control"
                            id="product_edit_description"
                            name="product_edit_description"
                            rows="4"
                            v-model="product.description"
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
                        <!-- Price -->
                        <div class="form-group col">
                            <label for="product_price">Price</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="product_edit_price"
                                    name="product_edit_price"
                                    v-model="product.price"
                                    aria-describedby="product_price"
                                    placeholder="Price"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Shopify Variant ID -->
                        <div class="form-group col">
                            <label for="shopify_variant_id">Shopify Variant ID</label>
                            <input
                            type="text"
                            class="form-control"
                            id="shopify_variant_id" 
                            v-model="product.shopify_variant_id"
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
                                <div v-if="product.thumbnail" class="image-thumb-wrapper">
                                    <label>Current Thumbnail</label>
                                    <img :src="product.thumbnail_url" class="thumb" />
                                </div>

                                <label for="thumbnail">Change Thumbnail</label>

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
                                <div v-if="product.thumbnail_hover" class="image-thumb-wrapper">
                                    <label>Current Thumbnail Hover</label>
                                    <img :src="product.thumbnail_hover_url" class="thumb" />
                                </div>

                                <label for="thumbnail_hover">Change Thumbnail Hover</label>

                                <input type="file" id="thumbnail_hover"
                                    @change="onThumbnailHoverChange"
                                    class="form-control-file"/>
                                <small class="form-text text-muted">
                                Optional Hover Image for listing page
                            </small>
                            </div>
                        </div>
                    </div>

                    <!-- product images -->      
                    <div class="row border bg-light px-2 py-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-images">Product Images</label>
                                <input 
                                    class="form-control-file mb-3"
                                    type="file" name="edit-images" id="edit-images" ref="images" multiple />
                                <small class="form-text text-muted mb-3">
                                    These are the images shown on the product detail page.<br/>
                                    You may upload multiple files by Ctrl/Cmd clicking filenames<br/> in the upload dialog that appears after clicking 'Choose Files'.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">       
                            <label>Current Product Images</label>
                            <div class="d-flex flex-wrap">
                                <div v-for="image in productImagesNotRemoved"  :key="image.id"
                                    class="border bg-white p-2 m-1 d-flex flex-column">
                                    <img :src="image.file_url" class="border mb-2" style="width: 80px;">
                                    <button class="btn btn-outline-danger btn-sm mt-auto justify-self-end" type="button"
                                        @click.prevent="removeImage(image.id)">Delete</button>
                            </div>
                            </div>
                        </div>
                        
                    </div> <!-- end product images -->
                </div> <!-- end product media -->
            </div>
            <!-- end main colum col-->

            <!-- sidebar col -->
            <div class="col">
                <div class="card">
                    <!-- Publish Flag -->
                    <div class="form-check published-check">
                        <input type="checkbox" class="form-check-input" id="is_published"
                            v-model="product.is_published">
                        <label class="form-check-label" for="is_published">Is Published</label>
                        <small class="form-text text-muted">
                            Controls if a product is visible in the store. 
                        </small>
                    </div>

                    <hr/>

                    <!-- category -->
                    <div class="form-group">
                        <label for="name">Category</label>
                        <div class="num-selected">
                            {{selectedProductsCount}} selected
                        </div>
                        <span v-for="(cat, idx) in product.categories" class="category" :key="cat.id+'d'">{{cat.name}}<span v-if="idx < (product.categories.length - 1)" class="comma">, </span>
                                </span>
                        <span v-for="cat in product_categories" class="category" :key="cat.id">
                            <div class="form-check published-check">
                                <input type="checkbox" class="form-check-input"
                                    id="product_edit_categories"
                                    name="product_edit_categories"
                                    v-model="selectedProductIDs" :value="cat.id">
                                <label class="form-check-label" for="is_published">
                                    <label v-if="!cat.store_page_id">
                                        [M]
                                    </label>
                                    <label v-else>
                                        [D]
                                    </label>
                                    {{cat.id}}{{cat.name}}
                                </label>
                            </div>
                            <!-- <span v-if="idx < (product.categories.length - 1)" class="comma">, </span> -->
                        </span>
                        <!-- <select
                            class="form-control"
                            id="product_edit_categories"
                            name="product_edit_categories"
                            v-model="category_ids">
                                <option value="" selected disabled>-- Select a Category --</option>
                                <option
                                    v-for="category in product_categories"
                                    :key="category.id"
                                    :value="category.id"
                                    >{{ category.name }}</option>
                        </select> -->
                        {{selectedProductIDs}}
                    </div>

                    <hr/>

                    <!-- Cart Purcahse Limit -->
                    <div class="form-group">
                        <label for="max_cart_quantity">Maximum Cart Quantity</label>
                        <input type="number" min="1" step="1"
                            class="form-control"
                            id="max_cart_quantity"
                            v-model="product.max_cart_quantity"
                            placeholder="Unlimited" />
                        <small class="form-text text-muted">
                            Limits the number of items a customer may add to their cart per purchase. 
                            <em>A blank value means there is no limit.</em>
                        </small>
                    </div>

                    <hr/>

                    <!-- Not For Sale -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="not_for_sale"
                            v-model="product.not_for_sale">
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
                            v-model="product.is_suggested">
                        <label class="form-check-label" for="is_suggested">Is Suggested Product</label>
                        <small class="form-text text-muted">
                            Used to add products in suggested products in cart.
                        </small>
                    </div>   
                </div>  
            </div>
        </div>

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
                    v-model="product.ext_purchase_url"
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
                    v-model="product.minimum_age"
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
                    v-model="product.current_inventory"
                    aria-describedby="current_inventory"
                  />
                </div>
              </div>
            </div>
----------------------->

        <!-- Product Sizes -->
        <div class="card size-row-section">
            <h5 class="card-title">Product Sizes</h5>

            <div v-for="(size, i) in product.sizes" class="form-row size-row" :key="i">
                <div class="col-lg-2">
                    <label>Size</label>
                    <input
                        type="text"
                        class="form-control"
                        :id="'size_' + i + '_size'"
                        :name="'product_edit_size_' + i + '_size'"
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
                            :name="'product_edit_size_' + i + '_price'"
                            v-model="size.price"
                            placeholder="5.00"
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
                        name="button" @click="removeSizeOption(i)" class="btn btn-danger">Delete</button>
                </div>
            </div>
            <!-- Add Size Button Row -->
            <div class="row pt-2 mt-2">
                <div class="col">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click.prevent="addSizeOption()"
                        >+ Add Product Size
                    </button>
                </div>
            </div>
        </div>

       
        

        <div class="btn-toolbar mt-2 pt-2">
            <div class="btn-group mr-4">
                <button type="submit" class="btn btn-primary" @click.prevent="saveProduct">Save</button>
             </div>
            
            <div class="btn-group mr-auto">
                <slot name="cancel-link"></slot>
            </div>

            <div class="btn-group">
            </div>

        </div>
  </div>
</template>

<script>
export default {
    props: {
        productProp: Object,
        saveUrl: String,
        successUrl: String,
    },
    data() {
        return {
            product: null,
            product_categories: product_categories,
            removedImages: [],
            newThumbnail: null,
            newThumbnailHover: null,
            errors: [],
            selectedProductIDs: [],
        };
    },
    created() {
        // product passed as a prop is readonly and can't be edited, make a deep copy
        this.product = Object.assign({},this.productProp);

        // The DB supports M2M on categories and products, but for that was old code
        // The store nav from pages to products works better if a product only lives in a single
        // category. This forces that.
        if( this.product.categories.length > 0 ) {
            this.product.categories.forEach( category => {
                this.selectedProductIDs.push(category.id);
            });
        }
     
    },

    methods: {
        async saveProduct() {
            this.errors = [];
            var formData = new FormData();
            formData.append("product", JSON.stringify(this.product));
            formData.append("categories", JSON.stringify(this.selectedProductIDs));
            formData.append("removedImages", JSON.stringify(this.removedImages));

            // Add Thumbnail and Hover Images if needed
            if(this.newThumbnail) {
                formData.append("thumbnail", this.newThumbnail);
            }
            if(this.newThumbnailHover) {
                formData.append("thumbnail_hover", this.newThumbnailHover);
            }

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
                    this.$toast.success('Product Saved');
                    window.location.href = this.successUrl;
                } else {
                    this.$toast.error('Unknown Error Saving Product');
                }

            }
            catch(error) {
                console.log('Error Saving Product', error, error.response, error.response.data);
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
            this.product.sizes.push({ size: null, price: null, id: null, shopify_variant_id: null, display_order: 10 });
        },
        removeSizeOption: function(i) {
            this.product.sizes.splice(i, 1);
        },
        removeImage: function(id) {

            this.removedImages.push(id);
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
          if (this.selectedProductIDs) {
              return this.selectedProductIDs.length;
          }
          return null;
      },
      store_id() {
          return this.product.store_id;
      },
      productImagesNotRemoved() {
          // The images not being removed
          return this.product.images.filter( img => !this.removedImages.includes(img.id));
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