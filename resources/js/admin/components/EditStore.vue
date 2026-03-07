<template>
  <form id="edit-store-app" @submit="editStore" action="/admin/store" method="post">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <h2>Edit Store</h2>
              <div v-if="errors" class="mt-4 pt-4">
                <errors :errors="errors"></errors>
              </div>
            </div>
          </div>

          <div class="row border-bottom mb-4">
            <div class="col-8">
              <div class="form-group">
                <label for="name">Store Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  name="edit_name"
                  v-model="store.name"
                  aria-describedby="name"
                  placeholder="Store Name"
                  required
                />
              </div>
            </div>
      
            <div class="col-4">
              <div class="form-group">
                <label for="name">Store Slug</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  name="slug"
                  v-model="store.slug"
                  placeholder="Slug"
                  required
                />
                <small class="form-text text-muted">The slug is used for unique URL for Store.</small>
              </div>
            </div>
          </div>

          <div class="row mb-4 border-bottom">
                <div class="col">
                    <div class="form-group border-bottom pb-4">                
                        <div v-if="store.cart_bg_image" class="image-thumb-wrapper">
                            <label>Current Cart Background Image</label>
                            <img :src="store.cart_bg_image_url" class="thumb" />
                        </div>
                    

                        <label for="background_image">Change Cart Background Image</label>

                        <input type="file" id="background_image"
                            @change="onCartBackgroundImageChange"
                            class="form-control-file"
                            placeholder="URL for a background video" />
                    </div>

                    <div class="form-group">                
                        <div v-if="store.photo_booth_bg_image" class="image-thumb-wrapper">
                            <label>Current Photo Booth Background Image</label>
                            <img :src="store.photo_booth_bg_image_url" class="thumb" />
                        </div>
                    

                        <label for="background_image">Change Photo Booth Background Image</label>

                        <input type="file" id="background_image"
                            @change="onPhotoBoothBackgroundImageChange"
                            class="form-control-file"
                            placeholder="URL for a background video" />
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="intro_video">Store Intro Video</label>
                        <input type="url" id="intro_video"
                            v-model="store.intro_video"
                            class="form-control"
                            placeholder="URL for a intro video"/>
                    </div>
                </div>
          </div>

          <div class="row">
            <div class="col">
                <div class="form-group form-check"> 
                  <input type="checkbox" class="form-check-input" id="is_test_mode" 
                        v-model="store.is_test_mode"/>
                  <label for="is_test_mode">Is Test Mode</label>
                  <small class="form-text text-muted">A test mode store can only be viewed by admin and test users.</small>
                </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="edit-store">Edit Store</button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      store: false,
      errors: [],
      newCartBackgroundImage: null,
      newPhotoBoothBackgroundImage: null,
    };
  },
  methods: {
    fileInputChange(event) {
        let files = event.target.files;
        if(files.length > 0 ) {
            return files[0];
        } else {
            return null;
        }
    },
    onCartBackgroundImageChange(event) {
        this.newCartBackgroundImage = this.fileInputChange(event);
    },

    onPhotoBoothBackgroundImageChange(event) {
         this.newPhotoBoothBackgroundImage = this.fileInputChange(event);
    },

    loadStore: function(id) {
      return axios
        .get("/admin/store/" + id)
        .then(response => {
          this.store = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    async editStore(e) {
        e.preventDefault();

        this.errors = [];

        let formData = new FormData();
        let formFields = [
            "name",
            "slug",
            "is_test_mode",
            "intro_video",
        ];
        
        formFields.forEach(dataKey => {
            if(this.store[dataKey]) {
                formData.append(dataKey, this.store[dataKey]);
            }
        });

        // ADD images if needed
        if( this.newCartBackgroundImage ) {
            formData.append('cart_bg_image', this.newCartBackgroundImage);
        }
        if(this.newPhotoBoothBackgroundImage) {
            formData.append('photo_booth_bg_image', this.newPhotoBoothBackgroundImage)
        }
        try {
            const response = axios.post("/admin/store/" + this.store.id, formData, {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            });

            // Success Redirect
            window.location.href = '/admin/stores';
    
        }
        catch(error) {
            let err_msg = 'Error Saving Store';
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
    }
  }
};
</script>
