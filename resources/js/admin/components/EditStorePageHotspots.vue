<template>
    <div class="container-fluid store-page-hotspots">
        <div v-if="errors.length > 0" class="row mt-2">
            <errors :errors="errors" class="col-12"></errors>
        </div>
        <div class="hs-editor-row">
            <div class="hotspots" :class="{'disabled': is_processing }"
                 :style="{ backgroundImage: 'url(' + page.background_image_url + ')' }"
                 @click="newHotspot">
                
                <a v-for="hotspot in page.hotspots" :key="hotspot.id"
                    @click.prevent.stop="openHotspotEdit(hotspot, $event)" class="hotspot"
                    :class="hotspotClasses(hotspot)"
                    :style="{ left: hotspot.x + '%', top: hotspot.y + '%' }">
                    <span class="name">{{ hotspot.name }}</span>
                </a>
            </div>
        </div>
        <div class="card shadow hotspot-editor-wrapper">
            <transition name="admin-fade" mode="out-in" appear>
                <edit-hotspot
                    v-if="currentHotspot"
                    :key="currentHotspot.id"
                    :hotspot-to-edit="currentHotspot"
                    :hotspot-types="hotspotTypes"
                    :all-pages="allPages"
                    :products="products"
                    :categories="categories"
                    :is-processing="is_processing"
                    @save="saveHotspotClick"
                    @delete="deleteHotspotClick"
                    @cancel="cancelHotspotEditClick" />
                <small v-else key="instructions">Click the image to create a new hotspot</small>
            </transition>
        </div><!-- end hotspot-editor-wrapper -->
    </div>
</template>

<script>
    const MIN_X = 0;
    const MAX_X = 100;
    const MIN_Y = 0;
    const MAX_Y = 100;

    import EditHotspot from "./EditHotspot.vue";

    export default {
        name: "EditStorePageHotspots",
        components: {
            EditHotspot,
        },
        data() {
            return {
                errors: [],
                is_processing: false,
                currentHotspot: null,
            }
        },
        props: {
            page: Object,
            hotspotTypes: Array,
            createUrl: String,
            hotspotUrlBase: String,
            allPages: Array,
            products: Array,
            categories: Array,
        },
        mounted() {
            console.log("Component mounted.");
        },
        methods: {
            newHotspot(event){
                console.log('new hotspot');
                
                if(this.is_processing) {
                    // bail if we are processing
                    return;
                }

                var hotspot = {
                    id: null,
                    type: '',
                    value: '',
                    x: (event.offsetX / event.target.offsetWidth) * 100, //(event.offsetX / event.target.width) * 100,
                    y: (event.offsetY / event.target.offsetHeight) * 100, // (event.offsetY / event.target.height) * 100,
                    name: '',
                    slug: '',
                    transition_video: null,
                    display_order: 10,
                    idx: this.page.hotspots.length, // the index of this will be the next elemin the array
                };
                this.page.hotspots.push(hotspot);
                
                this.openHotspotEdit(hotspot, event);
            },
            openHotspotEdit(hotspot, event){
                if(this.is_processing) {
                    // bail if we are processing
                    return;
                }
                this.currentHotspot = hotspot;
            },
            closeHotspotEdit(){
                this.currentHotspot = null;
            },
            saveHotspotClick(hotspot) {
                console.log('saveHotspot clicked()');
                
                this.errors = [];

                if( this.is_hotspot_valid(hotspot) ) {
                    console.log('save hot spot');
                
                    if( this.currentHotspot.id ) {
                        this.updateHotspot(hotspot);
                    } else {
                        this.createHotspot(hotspot);
                    }
                }
            },
            cancelHotspotEditClick() {
                // cancel editing
                this.closeHotspotEdit();
            },
            async deleteHotspotClick(hotspot) {
                if( confirm("Are you sure you want to delete the Hotspot?") ) {
                    this.errors = [];
                    if(hotspot.id) {
                        // If the hotspot has been saved delete it from the server
                        const successs = await this.deleteSavedHotspot(hotspot);
                        if(successs) {
                            this.deleteLocalHotspot(hotspot);
                            this.$toast.success('Hotspot Deleted');
                        }
                    } else {
                        this.deleteLocalHotspot(hotspot);
                    }
                }
            },

            is_x_pos_valid(x) {
                if(_.isNumber(parseFloat(x)) && x <= 99 && x >= 1) {
                    return true;
                } else {
                    return false;
                }
            },
            is_y_pos_valid(y) {
                if(_.isNumber(parseFloat(y)) && y <= 99 && y >= 1) {
                    return true;
                } else {
                    return false;
                }
            },
            is_hotspot_valid(hotspot) {
                console.log(hotspot.name);
                let is_valid = true;

                if(!hotspot.name) {
                    is_valid = false;
                    this.errors.push("Name required");
                }
                if(!hotspot.display_order || hotspot.display_order < 1) {
                    is_valid = false;
                    this.errors.push("Display Order required and must be greater than 1");
                }
                if(!hotspot.type) {
                    is_valid = false;
                    this.errors.push("Type Required");
                }
              
                // TODO Add validity check for hotspot value
                
                if(! this.is_x_pos_valid(hotspot.x)) {
                    is_valid = false;
                    this.errors.push("X Position Invalid");
                }

                 if(! this.is_y_pos_valid(hotspot.y)) {
                     is_valid = false;
                     this.errors.push("Y Position Invalid");

                }

                return is_valid;
            },
            async updateHotspot(hotspot) {
                try {
                    this.is_processing = true;
                    console.log('update existing hotspot: ' + hotspot.id);
                    const response = await axios.patch(`${this.hotspotUrlBase}/${hotspot.id}`, hotspot);

                    if(response.data.success) {
                        let saved_hotspot = response.data.hotspot;    
                        // Hide Edit Hotspot editor. Show success message;
                        // update local copy of hotspot
                        this.updateLocalHotspot(hotspot);
                        this.$toast.success('Hotspot Saved');
                        this.closeHotspotEdit();

                    } else {
                        this.errors.push("Unknown Error Saving Hotspot");
                    }
                }
                catch(error) {
                    this.showError(error, 'Error Saving Hotspot');
                }
                finally {
                    this.is_processing = false;
                }
            },
            async createHotspot(hotspot) {
                try {
                    this.is_processing = true;
                    console.log('create new hotspot');
                    const response = await axios.post(this.createUrl, hotspot);
                    
                    if(response.data.success) {
                        let saved_hotspot = response.data.hotspot;
                        // set the id of newely created hotspot
                        hotspot.id = saved_hotspot.id,
                        this.addLocalHotspot(hotspot);
                        this.$toast.success('Hotspot Created');
                        this.closeHotspotEdit();
                    } else {
                        this.errors.push("Unknown Error Creating Hotspot");
                    }
                }
                catch(error) {
                    console.log(error);
                    this.showError(error, 'Error Creating Hotspot');
                }
                finally {
                    this.is_processing = false;
                }
            },
            addLocalHotspot(hotspot) {
                console.log('add local hotspot', hotspot);
                let idx = hotspot.idx;
                this.page.hotspots[idx] = hotspot;
                hotspot.idx = null;
            },
            updateLocalHotspot(hotspot) {
                let idx = this.page.hotspots.findIndex((el) => el.id === hotspot.id);
                console.log('updateLocalHotspot', idx);
                if(idx >= 0) {
                    // saving hotpots
                    console.log('saving hotspots');
                    this.page.hotspots[idx] = hotspot;
                }
            },
            deleteLocalHotspot(hotspot) {
                // Delete Local Copy of currentHotspot
                let idx = null;

                // Get the index of the hotspot to remove
                if(hotspot.id) {
                    // for "saved" hotspots
                    idx = this.page.hotspots.findIndex((el) => el.id === hotspot.id);
                } else if(hotspot.idx !== null) {
                    // for new hotspots
                    idx = hotspot.idx;
                }

                // if we have a valid index remove hotspot
                if(idx !== null) {
                    // remove form hotspot list
                    this.page.hotspots.splice(idx, 1);
                    this.closeHotspotEdit();
                } else {
                    console.log('error deleting local hotspot', idx, hotspot);
                    this.errors.push('Error Deleting Local Hotspot')
                }
            },
            async deleteSavedHotspot(hotspot) {
                try {
                    this.is_processing = true;
                    console.log('Delete hotspot on server: ' + hotspot.id);
                    const response = await axios.delete(`${this.hotspotUrlBase}/${hotspot.id}/delete`, hotspot);
                    this.is_processing = false;
                    if(response.data.success) {
                        // Success
                        return true;

                    } else {
                        this.errors.push("Unknown Error Deleting Hotspot");
                        return false;
                    }
                }
                catch(error) {
                    this.is_processing = false;
                    this.showError(error, 'Error Deleting Hotspot');
                    return false;
                }
            },
            showError(error, msg) {
                let err_msg = msg;
                if( error.response && error.response.data.message) {
                    err_msg += ' - ' + error.response.data.message;
                }
                this.errors.push(err_msg);
                if(error.response && error.response.data.errors) {
                    for( const prop in error.response.data.errors) {
                        this.errors.push(error.response.data.errors[prop][0]);
                    }
                }
            },
            hotspotClasses(hotspot) {
                return {
                    'is-selected': this.currentHotspot != null && this.currentHotspot.id == hotspot.id,
                    'is-new': hotspot.id == null
                }
            }
        },
        computed: {
            
            is_new_hotspot() {
                return this.currentHotspot.id == null;
            },
            save_button_copy() {
                if(this.is_new_hotspot) {
                    return "Create New"
                } else {
                    return "Update"
                }
            },
            store() {
                this.page.store;
            },
        },
    };
</script>

<style scoped lang="scss">
    .store-page-hotspots {
        position: relative;
    }
    .hs-editor-row {
        overflow:  scroll;
        position: relative;
    }
    .hotspots {
        //width: 100%;
        height: 75vh;
        position: relative;
        background-color: #333;
       
        background-repeat: no-repeat;
        background-position: center;

        width: 132vh;
        background-size: contain;

        &.disabled {
            opacity: 0.4;
        }
    }

    .hotspot {
        position: absolute;
        width: 40px;
        height: 40px;
        transform: translate(-50%, -50%);
        display: block;
        border: 2px solid white;
        border-radius: 100%;
        background-color: rgba(#222, 0.5);
        cursor: pointer;

        .name {
            display: none;
            color: white;
            position: absolute;
            font-size: 0.8rem;
            background-color: rgba(#000, 0.4);
            width: max-content;
            top: 50%;
            left: 100%;
            transform: translate(10px, -50%);
        }

        &:hover {
            transform: translate(-50%, -50%) scale(1.2);
            background-color: rgba(#333, 0.7);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);

            .name {
                display: inline-block;
                transform: translate(10px, -50%) scale(0.8);
            }
        }
    }
    .hotspot.is-selected {
        border-color: #27C18A;
        background-color: rgba( #27C18A, 0.4);
        transform: translate(-50%, -50%) scale(1.2);
    }
    .hotspot.is-new {
        border-color: #80D1EA;
        background-color: rgba( #80D1EA, 0.6);
    }

    // Hot Spot Editor
    .hotspot-editor-wrapper {
        border: 1px solid #BBB;
        position: absolute; 
        z-index: 1000;
        right: -20px;
        top: -20px;
        width: 260px;
        padding: 20px;
    }
    

    // transition 
    .admin-fade-enter-active {
        transition: all 0.2s ease;
    }
    .admin-fade-leave-active {
        transition: all 0.2s ease;
    }
    .admin-fade-enter {
        opacity: 0;
    }
    .admin-fade-leave-to {
        opacity: 0;
    }
</style>