<template>
    <div class="product-list">
        
        <errors v-if="errors" :errors="errors"></errors>
    
        <header class="table-control-header mb-2">
            <div class="num-selected total" v-if="totalProducts > products.length">{{ totalProducts }} Total Products</div>
            <div class="num-selected">
                {{selectedProductsCount}} of {{ products.length}} selected
            </div>
            <div class="num-selected">
                <input v-model="searchTerm" type="text">
            </div>
            <transition name="fade" mode="out-in">
                <div class="btn-toolbar" v-if="haveSelectedProducts" key="selectedProductToolbar">
                    <div class="btn-group mr-4">
                        <button type="button" class="btn btn-publish"
                                @click.prevent="updateSelectedPublishStatusClick(true)">Publish Selected</button>
                        
                        <button type="button" class="btn btn-unpublish"
                                @click.prevent="updateSelectedPublishStatusClick(false)">Unpublish Selected</button>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-danger"
                                @click.prevent="deleteSelectedProductsClick">Delete Selected</button>
                    </div>    
                </div>
            </transition>
        </header>

        <div class="table-responsive">
                <table class="table table-striped product-table">
                        <thead class="thead-light">
                            <th scope="col" class="cell-select">
                                <div class="form-check">
                                        <input type="checkbox" 
                                            class="form-check-input position-static"
                                            v-model="selectAll"/>
                                    </div>
                            </th>

                            <th scope="col" class="cell-thumb">Thumbnail</th>
                            <th @click="sort('name')" scope="col" class="cell-name">
                                Name
                                <a v-if="currentSort==='name' && currentSortDir==='asc'" >▴</a>
                                <a v-else-if="currentSort==='name' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('categories')" scope="col" class="cell-categories">
                                Categories
                                <a v-if="currentSort==='categories' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='categories' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('is_published')" scope="col" class="cell-status">
                                Status
                                <a v-if="currentSort==='is_published' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='is_published' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('max_cart_quantity')" scope="col" class="cell-max-cart-qty">
                                Max Cart Quantity
                                <a v-if="currentSort==='max_cart_quantity' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='max_cart_quantity' && currentSortDir==='desc'">▾</a>
                                <a v-else> &nbsp;&nbsp;&nbsp;&nbsp; </a>
                            </th>
                            <th @click="sort('created_at')" scope="col" class="cell-date">
                                Created At
                                <a v-if="currentSort==='created_at' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='created_at' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('updated_at')" scope="col" class="cell-date">
                                Updated At
                                <a v-if="currentSort==='updated_at' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='updated_at' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th scope="col" class="cell-buttons"></th>
                        </thead>
                        <tbody>
                            <tr v-for="product in sortedProducts" :key="product.id">
                                <th class="cell-select" scope="row">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                            v-model="selectedProductIDs"
                                            class="form-check-input position-static" :value="product.id" >
                                    </div>
                                </th>
                                <td class="cell-thumb">
                                    <img class="thumb" :src="product.thumbnail_url" style="width: 100px;"/>        

                                </td>
                                <td class="cell-name">
                                    <a :href="productEditURL(product)" title="Edit">{{ product.name }}</a>
                                </td>
                                <td class="cell-categories">
                                    <span v-for="(cat, idx) in product.categories" class="category" :key="cat.id">{{cat.name}}<span v-if="idx < (product.categories.length - 1)" class="comma">, </span>
                                    </span>
                                </td>
                                <td class="cell-status">
                                    <span v-if="product.is_published" class="badge badge-pill published">Published</span>
                                    <span v-else class="badge badge-pill unpublished">Unpublished</span>
                                </td>

                                <td class="cell-max-cart-qty">
                                    <span v-if="product.max_cart_quantity">

                                        {{ product.max_cart_quantity }}
                                    </span>
                                    <span v-else>
                                        -
                                    </span>
                                </td>

                                <td class="cell-date">
                                    {{ product.created_at | dateFormat }}
                                </td>

                                <td class="cell-date">
                                    {{ product.updated_at | dateFormat }}
                                </td>

                                <td class="text-right button-cell">
                                    <a :href="productEditURL(product)"
                                    class="btn btn-primary btn-sm">Edit</a>
                                <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    @click="deleteSingleProductClick(product)">Delete</button>
                                

                                </td>
                            </tr>
                        </tbody>
                </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "ProductList",
    props: {
        initialProducts: {
            type: Array,
            required: true,
        },
        totalProducts: {
            type: Number,
            required: true,

        },
        storeId: {
            type: Number,
            required: true,
        }
    },
    data: () => ({
        products: null,
        selectedProductIDs: [],
        errors: [],
        is_processing: false,
        searchTerm: '',
        currentSort:'name',
        currentSortDir:'asc',
        pageSize: 500,
        currentPage:1
    }),
    created() {
        // copy initial products in case we need to mutate in the future
        this.products = [...this.initialProducts];
    },
    watch: {
        pageSize: function (){
            this.currentPage = 1
        }
    },
    computed: {
        filteredProducts() {
            let st = this.searchTerm;
            return this.products.filter(function f(product) {
                if(st == '') return true;
                return product.name.toLowerCase().includes(st.toLowerCase()) ||
                    (product.categories && (product.categories.filter(f)).length);
            });
        },
        sortedProducts() {
            return this.filteredProducts.sort((a,b) => {
                let modifier = 1;
                if(this.currentSortDir === 'desc') modifier = -1;
                if(a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
                if(a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
                return 0;
            }).filter((row, index) => {
                let start = (this.currentPage-1)*this.pageSize;
                let end = this.currentPage*this.pageSize;
                if(index >= start && index < end) return true;
            });
        },
        selectedProductsCount() {
            return this.selectedProductIDs.length;
        },
        haveSelectedProducts() {
            return this.selectedProductsCount > 0;
        },
        selectAll: {
            get() {
                return this.products.length > 0 ? this.selectedProductIDs.length == this.products.length : false;
            },
            set(value) {
                let selected = [];
                if( value ) {
                    this.products.forEach( product => {
                       selected.push(product.id); 
                    });
                }
                this.selectedProductIDs = selected;
            }

        }
    },
    filters: {
    },
    methods: {
        sort:function(s) {
            if(s === this.currentSort) {
                this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
            }
            this.currentSort = s;
        },
        nextPage:function() {
        if((this.currentPage*this.pageSize) < this.products.length) this.currentPage++;
        },
        prevPage:function() {
        if(this.currentPage > 1) this.currentPage--;
        },

        productEditURL(product) {
            return `/admin/store/${product.store.id}/products/${product.id}/edit`;
        },
        async deleteSingleProductClick(product) {
            console.log('Delete Prouct Click', product.id);
            if( confirm('Are you sure you want to delete this product? ') ) {
                console.log('Do delete');
                await this.deleteSingleProduct(product);
            }
            return false;
        },

        async deleteSingleProduct(product) {
            // Delete a single product

            let deleteURL = `/admin/store/${this.storeId}/products/${product.id}/delete`;
            try {
                this.is_processing = true;
                // this redirects so we should NEVER get a response
                const response = await axios.delete(deleteURL);
                if(response.data.success) {
                    //  reload page, we changed the list
                    this.$toast.success('Product Deleted');
                    window.location.reload();
                } else {
                    this.errors.push('Unknown Error Saving Page');
                }
            }
            catch(error) {
                console.log('Error Deleting Product', error);
                let err_msg = 'Error Deleting Product';
                if( error.response && error.response.data.message) {
                        err_msg += ' - ' + error.response.data.message;

                }
                this.errors.push(err_msg);
                if(error.response && error.response.data.errors) {
                    for( const prop in error.response.data.errors) {
                        this.errors.push(error.response.data.errors[prop][0]);
                    }
                }
            }
            finally {
                this.is_processing = false;
            }

        },
        // Delete multiple products
        async deleteSelectedProductsClick() {
            if( confirm('Are you sure you want to delete these products? ') ) {
                await this.deleteProducts(this.selectedProductIDs);
            }

            return false;
        },
        // delete a list of products by an array of ids
        async deleteProducts(productList) {
            let deleteMultipleURL = `/admin/store/${this.storeId}/products/delete-many`;;
            try {
                this.is_processing = true;
                // this redirects so we should NEVER get a response
                let data = {'productIDs': productList};
                const response = await axios.post(deleteMultipleURL, data);
                if(response.data.success) {
                    //  reload page, we changed the list
                    this.$toast.success('Products Deleted');
                    window.location.reload();
                } else {
                    this.errors.push('Unknown Error Saving Page');
                }
            }
            catch(error) {

                console.log('Error Deleting Product', error);
                let err_msg = 'Error Deleting Product';
                if( error.response && error.response.data.message) {
                        err_msg += ' - ' + error.response.data.message;
                }
                this.errors.push(err_msg);
                if(error.response && error.response.data.errors) {
                    for( const prop in error.response.data.errors) {
                        this.errors.push(error.response.data.errors[prop][0]);
                    }
                }
            }
            finally {
                this.is_processing = false;
            }
        },

        async updateSelectedPublishStatusClick(is_published) {

            let confirmMsg = '';
            if(is_published) {
                confirmMsg = 'Are you sure you want to Publish these products?'
            } else {
                confirmMsg = 'Are you sure you want to Unpublish these products?'
            }
            if( confirm(confirmMsg) ) {
                await this.updateSelectedPublishStatus(this.selectedProductIDs, is_published);
            }
            return false;
        },

         // change publish status for list of products
        async updateSelectedPublishStatus(productList, is_published) {
            let updatePublishStatusMultipleURL = `/admin/store/${this.storeId}/products/update-publish-status-many`;;
            try {
                this.is_processing = true;

                // this redirects so we should NEVER get a response
                let data = {'productIDs': productList, 'is_published': is_published};
                const response = await axios.post(updatePublishStatusMultipleURL, data);
                if(response.data.success) {
                    //  reload page, we changed the list
                    this.$toast.success('Products Deleted');
                    window.location.reload();
                } else {
                    this.errors.push('Unknown Error Saving Page');
                }
            }
            catch(error) {
                let err_msg = 'Error Setting Publish Status Product';
                console.log(err_msg, error);
                if( error.response && error.response.data.message) {
                    err_msg += ' - ' + error.response.data.message;
                }
                this.errors.push(err_msg);
                if(error.response && error.response.data.errors) {

                    for( const prop in error.response.data.errors) {
                        this.errors.push(error.response.data.errors[prop][0]);
                    }
                }
            }
            finally {
                this.is_processing = false;
            }
        },
    },
}
</script>

<style>
</style>