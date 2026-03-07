<template>
    <div class="hotspot-editor" key="hotspot-editor">
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="hs_name">Name</label>
                    <input type="text" class="form-control" id="hs_name"
                        required
                        v-model.trim="hotspot.name">
                    <small class="form-text text-muted">
                        Used for Hotspot tooltip or text version
                    </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="hs_slug">CSS Classes</label>
                    <input type="text" class="form-control" id="hs_slug"
                        v-model.trim="hotspot.slug">
                    <small class="form-text text-muted">
                        Used as a CSS class name applied to the hotspot
                    </small>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="hs_display_order">Display Order</label>
                    <input type="number" min="1"
                        required
                        class="form-control" id="hs_display_order"
                        v-model.trim="hotspot.display_order">
                    <small class="form-text text-muted">
                        Order of hotspots if displayed as a list
                    </small>
                </div>
                <div class="form-group">
                    <label for="hotspot_type">Type</label>

                    <select class="form-control" id="hotspot_type"
                        required
                        v-model.trim="hotspot.type">
                        <option value="" selected disabled>-- Select a Type --</option>
                        <option 
                            v-for="type in hotspotTypes"
                            :key="type"
                            :value="type">{{type}}</option>
                    </select>
                </div>
            
                <div class="form-group">
                    
                    <template v-if="hotspot.type == 'PAGE'">
                        <label for="hotspot_value">Select a Page</label>
                        <select class="form-control" id="hotspot_value" 
                            key="store-pages"
                            required 
                            v-model.trim="hotspot.value">
                            <option value="" selected disabled>-- Select a Page --</option>
                            <option 
                                v-for="page in allPages"
                                :key="page.id"
                                :value="page.slug">{{page.name}}</option>
                        </select>
                    </template>
                    <template v-else-if="hotspot.type == 'PRODUCT_CATEGORY'">
                        <label for="hotspot_value">Select a Category</label>
                        <select class="form-control" id="hotspot_value" 
                            key="product-category"
                            required 
                            v-model.trim="hotspot.value">
                            <option value="" selected disabled>-- Select a Category --</option>
                            <option 
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id">{{category.name}}</option>
                        </select>
                    </template>
                    <template v-else-if="hotspot.type == 'PRODUCT'">
                        <label for="hotspot_value">Select a Product</label>
                        <select class="form-control" id="hotspot_value"
                            key="products"
                            required
                            v-model.trim="hotspot.value">
                            <option value="" selected disabled>-- Select a Product --</option>
                            <option
                                v-for="product in products"
                                :key="product.id"
                                :value="product.id">{{product.name}}</option>
                        </select>
                    </template>
                    <template v-else-if="hotspot.type == 'HOME'">
                    </template>
                    <template v-else-if="hotspot.type == 'PHOTO-BOOTH'">
                    </template>
                    <template v-else>
                        <label for="hotspot_value">Value</label>
                        <input type="text" class="form-control"
                            v-model.trim="hotspot.value" />
                        <small class="form-text text-muted" v-html="value_help_text"></small>
                    </template>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="hs_video">Transition Video</label>
                    <input type="text" class="form-control" id="hs_video"
                        placeholder="URL for a transition video"
                        v-model.trim="hotspot.video">
                    <small class="form-text text-muted">
                        A quick video that plays before the hotspot click request is ran.
                    </small>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="hs_icon_url">Icon URL</label>
                    <input type="text" class="form-control" id="hs_icon_url"
                        placeholder="URL for the hotspot icon"
                        v-model.trim="hotspot.icon_url">
                    <small class="form-text text-muted">
                        Optional: Icon image URL to display on the hotspot. If empty, uses the slug-based styling.
                    </small>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="hotspot_pos_x">X</label>
                    <input type="number" class="form-control" id="hotspot_pos_x"
                        min="0" max="100"
                        required
                        v-model.trim="hotspot.x" />
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <label for="hotspot_pos_y">Y</label>
                    <input type="number" class="form-control" id="hotspot_pos_y"
                        min="0" max="100"
                        required
                        v-model.trim="hotspot.y" />
                </div>
            </div>
        </div>
    
        <div class="button-bar">
            <button type="button" 
                :disabled="isProcessing"
                class="btn btn-primary btn-sm mr-1" @click="$emit('save', hotspot)">
                {{save_button_copy}}
            </button>

            <button type="button"
                :disabled="isProcessing"
                class="btn btn-secondary btn-sm mr-3" @click="$emit('cancel')">
                Cancel
            </button>
            <button type="button"
                :disabled="isProcessing"
                class="btn btn-danger btn-sm" @click="$emit('delete', hotspot)">
                Delete
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditHotSpot",
    props: {
        hotspotToEdit: {
            type: Object,
            required: true,
        },
        hotspotTypes: {
            type: Array,
            required: true,
        },
        allPages: { 
            type: Array,
            requried: true,
        },
        products: {
            type: Array,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
        },
        isProcessing: {
            type: Boolean,
            required: true,
        }
    },
    data() {
        return {
            value_help_choices: {
                '': 'Select a Hot Spot Type',
                'PAGE': 'Value is the PAGE SLUG',
                'PRODUCT': 'Value is PRODUCT ID',
                'PRODUCT_CATEGORY': 'Value is CATEGORY ID',
                'HOME': 'Value is ignored. Links to the store\'s home page'
            },
            // Make a copy of the hotspot so we don't change the parent
            hotspot: {...this.hotspotToEdit},
        };
    },
    computed: {
        value_help_text() {
            return this.value_help_choices[this.hotspot.type];
        },
        is_new_hotspot() {
            return this.hotspot.id == null;
        },
        save_button_copy() {
            if(this.is_new_hotspot) {
                return "Create"
            } else {
                return "Update"
            }
        },
        hotspotType() {
            return this.hotspot.type;
        }
    },
    watch: {
        hotspotType(newHSType, oldHSType) {
            this.hotspot.value = null;
        }
    }
}
</script>

<style lang="scss" scoped>
    .hotspot-editor {
        .btn {
            display: inline-block;
        }
        label {
            margin-bottom: 0.25rem;
        }
        .form-control {
            height: 2rem;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        small {
            font-size: 11px; 
        }
    }

</style>