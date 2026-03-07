<template>
    <div class="edit-photo-booth-backdrop">
        <div v-if="errors.length > 0" class="col-12 mt-2">
            <errors :errors="errors"></errors>
        </div>
        <div class="row">
            <div class="col-5">
                <div class="form-group">
                    <div v-if="backdrop.image" class="image-thumb-wrapper">
                        <label>Image</label>
                        <img :src="backdrop.image_url" class="thumb" />
                    </div>

                    <label for="image">Image</label>
                    <input type="file" id="image" @change="onImageChange" required
                        class="form-control-file"/>
                    <small class="form-text text-muted">Image must be 1080 x 1080</small>
                </div>
            </div>
            <div class="col-5">
                <div class="form-group">
                    <label for="caption">Caption</label>
                    <input type="text" class="form-control" id="caption" v-model="backdrop.caption"/>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="text" class="form-control" id="display_order" v-model="backdrop.display_order"/>
                </div>
            </div>
        </div>
        <div class="row mt-2 pt-4 border-top d-flex justify-content-end">
            <button type="button" class="btn btn-dark mr-2" @click="$emit('cancel')">Cancel</button>
            <button :disabled="isSaving" type="button" class="btn btn-primary" @click="save">Save</button>
        </div>
    </div>
</template>

<script>
import ErrorLogMixin from '../mixins/ErrorLogMixin';

export default {
    name: "EditPhotoBoothBackdrop",
    props: {
        backdrop: Object,
        saveUrl: String,
        successUrl: String
    },
    mixins: [ErrorLogMixin],
    data() {
        return {
            isSaving: false,
            newImage: null,
        };
    },
    methods: {
        onImageChange(event) {
            let files = event.target.files;
            if( files.length > 0 ) {
                this.newImage = files[0];
            } else {
                this.newImage = null;
            }
        },
        async save() {
            this.errors = []
            console.log(this.backdrop);

            this.isSaving = true;
            let formData = new FormData();
            Object.keys(this.backdrop).forEach( (key) => {
                let value = this.backdrop[key];
                if( key != 'image' && value ) {
                    // skip readding the image
                    formData.append(key, value);
                }
            });

            // Add the Background Image    
            if( this.newImage ) {
                formData.append('image', this.newImage);
            }

            try {
                const response = await axios.post(this.saveUrl, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                });
                if(response.data.success) {
                    //this.page = response.data.store_page;
                    window.location.href = this.successUrl;
                } else {
                    this.logError('Unknown Error Saving Backdrop');
                }

            }
            catch(error) {
                this.logError('Error Creating Backdrop', error);
            }
            finally {
                this.isSaving = false;
            }
        }
    }
}
</script>

<style scoped lang="scss">
    .image-thumb-wrapper {
        label {
            display: block;
        }
        img {
            display: block;
            max-width: 100px;
            max-height: 120px;
            margin: 10px 0;
            box-shadow: 2px 4px 4px rgba(#000, 0.2);
        }
    }
</style>