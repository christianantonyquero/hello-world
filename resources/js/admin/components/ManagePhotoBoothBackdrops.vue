<template>
    <div class="edit-photo-booth-backdrops">
        <div v-if="errors.length > 0" class="col-12 mt-2">
            <errors :errors="errors"></errors>
        </div>
        
        <div v-if="loading" class="alert">Loading&hellip;</div>

        <div class="card"><div class="card-body">
            <h5 class="card-title">Photo Booth Backdrops</h5>
            <template v-if="!loading">

                <p v-if="backdrops.length == 0" class="text-warning">
                   No Backdrops Loaded
                </p>

                <table class="table admin-list-table backdrop-list-table">
                    <thead>
                        <th>Image</th><th>Caption</th><th>Display Order</th>
                        <th>&nbsp;</th>
                    </thead>
                    <tbody>
                        <tr v-for="b in backdrops" :key="b.id" class="backdrop">
                            <td>
                                <a :href="b.image_url" target="_blank">
                                    <img :src="b.image_url" style='max-height:100px;'/>
                                </a>
                            </td>
                            <td>
                                {{b.caption}}
                            </td>
                            <td>
                                {{b.display_order}}
                            </td>
                            <td class="button-cell">
                                <button class="btn btn-primary small" type="button"
                                    @click="showEditModal(b)">Edit</button>
                                <button class="btn btn-danger small" type="button"
                                    @click="showDeleteModal(b)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                

                <button class="btn btn-success" type="button"
                    @click="showCreateModal">New Backdrop</button>
            </template>
        </div></div>

        
        <b-modal size="lg" centered  ref="createModal" title="New Backdrop" hide-footer>
            <create-photo-booth-backdrop
                @cancel="hideCreateModal()"
                :create-url="backdropCreateUrl"
                :success-url="photoBoothAdminUrl" />
        </b-modal>

        <b-modal centered ref="deleteModal" title="Delete Backdrop" ok-variant="danger" ok-title="Delete"
            @ok="deleteBackdrop"
            @cancel="selected_backdrop = null">
            <p>Are you sure you want to delete this backdrop?</p>
        </b-modal>

        <b-modal size="lg" centered  ref="editModal" title="Edit Backdrop" hide-footer>
           <edit-photo-booth-backdrop
                @cancel="hideEditModal()"
                :backdrop="selected_backdrop"
                :save-url="update_url"
                :success-url="photoBoothAdminUrl" />
        </b-modal>
    </div>
</template>
<script>
import { BModal, VBModal } from 'bootstrap-vue';
import CreatePhotoBoothBackdrop from './CreatePhotoBoothBackdrop';
import EditPhotoBoothBackdrop from './EditPhotoBoothBackdrop';
import ErrorLogMixin from '../mixins/ErrorLogMixin';

export default {
    name: 'ManagePhotoBoothBackdrops',
    props: {
        loadBackdropUrl: String,
        store_id: Number,
        backdropCreateUrl: String,
        photoBoothAdminUrl: String,
    },
    mixins: [ErrorLogMixin],
    components: {
        BModal,
        VBModal,
        CreatePhotoBoothBackdrop,
        EditPhotoBoothBackdrop,
    },
    data() {
        return {
            is_processing: false,
            loading: false,
            backdrops: [],
            selected_backdrop: null,
        };
    },
    created() {
        this.loadBackdrops()
    },
    methods: {
        async loadBackdrops() {
            try {
                console.log('loading: ', this.loadBackdropUrl);
                const response = await axios.get(this.loadBackdropUrl);
                this.backdrops = response.data.backdrops;
            }
            catch(error)
            {
                this.logError('Error Loading Backdrops', error);
            }
        },
        showCreateModal() {
            this.selected_backdrop = null;
            this.$refs.createModal.show();
        },
        hideCreateModal() {
            this.selected_backdrop = null;
            this.$refs.createModal.hide();
        },
        showDeleteModal(backdrop, event) {
            this.selected_backdrop = backdrop;
            this.$refs.deleteModal.show();
        },
        async deleteBackdrop() {
            console.log('Delete Backdrop');
            if(! this.selected_backdrop) {
                this.logError('Error Deleting Backdrop. No Backdrop Selected');
                return false;
            }
            try {
                this.is_processing = true;
                const response = await axios.delete(this.delete_url, this.selected_backdrop); 
                if(response.data.success) {
                    this.loadBackdrops();
                } else {
                    this.logError('Unknown Error Deleting Backdrop');
                }    
            }
            catch(error) {
                this.logError('Error Deleting Hotspot', error);
            }
            finally {
                this.selected_backdrop = null;
                this.is_processing = false;
            }
        },
        showEditModal(backdrop, event) {
            this.selected_backdrop = backdrop;
            this.$refs.editModal.show();

        },
        hideEditModal() {
            this.selected_backdrop = null;
            this.$refs.editModal.hide();
        },
    },
    computed: {
        delete_url() {
            if(this.selected_backdrop) {
                return `${this.backdropCreateUrl}/${this.selected_backdrop.id}`;
            } else {
                return null;
            }
        },
        update_url() {
            if(this.selected_backdrop) {
                return `${this.backdropCreateUrl}/${this.selected_backdrop.id}`;
            } else {
                return null;
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    .edit-photo-booth-backdrops {

    }
    .backdrop-list-table {
        .button-cell {
            .btn {
                display: inline-block;
                margin: 0 5px;
            }
        }
    }
</style>