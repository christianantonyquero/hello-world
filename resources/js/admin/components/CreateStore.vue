<template>
  <form id="create-store-app" @submit="createStore" action="/admin/store" method="post">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
           <div class="row">
            <div class="col">
              <h2>Create New Store</h2>
              <div v-if="errors" class="mt-4 pt-4">
                <errors :errors="errors"></errors>
              </div>
            </div>
          </div>

          <div class="row mb-4 border-bottom">
            <div class="col-8">
              <div class="form-group">
                <label for="store_name">Store Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="store_name"
                  v-model="name"
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
                  v-model="slug"
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
                        <label for="background_image">Change Cart Background Image</label>

                        <input type="file" id="background_image"
                            @change="onCartBackgroundImageChange"
                            class="form-control-file"
                            placeholder="URL for a background video" />
                    </div>

                    <div class="form-group">                
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
                            v-model="intro_video"
                            class="form-control"
                            placeholder="URL for a intro video"/>
                    </div>
                </div>
          </div>

          <div class="row">
            <div class="col">
                <div class="form-group form-check"> 
                  <input type="checkbox" class="form-check-input" id="is_test_mode" 
                        v-model="is_test_mode"/>
                  <label for="is_test_mode">Is Test Mode</label>
                  <small class="form-text text-muted">A test mode store can only be viewed by admin and test users.</small>
                </div>
            </div>
          </div>
      
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="create-store">Create Store</button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import slugify from "slugify";

export default {
  data() {
    return {
      name: "",
      slug: "",
      is_test_mode: false,
      logo: "",
      errors: [],
      intro_video: "",
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
    createStore: function(e) {
      e.preventDefault();

      this.errors = [];

      var formData = new FormData();
      [
          "name",
          "slug",
          "is_test_mode",
          "intro_video",
      ].forEach(dataKey => {
        if(this[dataKey]) {
            formData.append(dataKey, this[dataKey]);
        }
      });

      // ADD images if needed
        if( this.newCartBackgroundImage ) {
            formData.append('cart_bg_image', this.newCartBackgroundImage);
        }
        if(this.newPhotoBoothBackgroundImage) {
            formData.append('photo_booth_bg_image', this.newPhotoBoothBackgroundImage)
        }

      return axios
        .post("/admin/store", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(function(response) {
          window.location.href = response.data.admin_url;
        })
        .catch((error) => {
            let err_msg = 'Error Creating Store';
            if( error.response && error.response.data.message) {
                err_msg += ' - ' + error.response.data.message;
            }
            this.errors.push(err_msg);
            if(error.response && error.response.data.errors) {
                for( const prop in error.response.data.errors) {
                    this.errors.push(error.response.data.errors[prop][0]);
                }
            }
            if(error.response.data.errors && error.response.data.errors.code){
              this.code_errors.push(error.response.data.errors.code[0]);
            }
            if(error.response.status == 413){
              this.errors.push('Images should be smaller than 5MB');
            }
        });
    },
  },
  watch: {
        "name"(newName, oldName) {
            this.slug = slugify(newName, {lower:true, strict: true});
        } 
    }
};
</script>
