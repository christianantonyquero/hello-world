<template>
    <div class="card">
        <div class="row">
          
          <div v-if="errors" class="col-12 mt-2">
              <errors :errors="errors"></errors>
          </div>

          <div class="col-sm-7">
            <div class="form-group">
                <label for="page_name">Page Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="page_name"
                  v-model="page.name"
                  aria-describedby="page name"
                  placeholder="Page Name"
                  required
                  autocomplete="off"
                />
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input
                  type="text"
                  class="form-control"
                  id="slug"
                  name="slug"
                  v-model="page.slug"
                  aria-describedby="slug"
                  placeholder="slug"
                  autocomplete="off"
                  required
                />
            </div>

            <div class="form-group">
                <label for="parent">Parent Page</label>
                <select class="form-control" id="parent"
                    v-model="page.parent_id">
                    <option selected :value="null">-- No Parent --</option>
                    <option
                        v-for="page in otherPages"
                        :key="page.id"
                        :value="page.id">{{page.name}}</option>
                </select>
            </div>

            <!--
            <div class="form-group">
                <label for="minimum_age">Minumum Age</label>
                <input
                  type="number"
                  class="form-control"
                  id="minimum_age"
                  v-model="page.minimum_age"
                  autocomplete="off"
                  aria-describedby="minimum_age"
                />
            </div>
            -->
            <hr/>
            
            <div class="form-group">
                <label for="html_content">Page Content (HTML)</label>
                <textarea id="html_content"
                    v-model="page.html_content"
                    class="form-control" rows="10"
                    placeholder="Add HTML content to the page"></textarea>
            </div>
          </div>

          <div class="col-sm-5">
            <div class="form-group">
                <div v-if="page.background_image" class="image-thumb-wrapper">
                    <label>Current Background Image</label>
                    <img :src="page.background_image_url" class="thumb" />
                </div>

                <label for="background_image">Change Background Image</label>

                <input type="file" id="background_image"
                    @change="onBackgroundImageChange"
                    class="form-control-file"
                    placeholder="URL for a background video" />
            </div>

            <div class="form-group">
                <label for="background_video">Background Video</label>
                <input type="url" id="background_video"
                    v-model="page.background_video"
                    class="form-control"
                    placeholder="URL for a background video"/>
            </div>

            <div class="form-group">
                <label for="intro_video">Intro Video</label>
                <input type="url" id="intro_video"
                    v-model="page.intro_video"
                    class="form-control"
                    placeholder="URL for an intro video"/>
                <small class="form-text text-warning" >
                   Do not use the intro video for the transitions videos between pages.<br>
                   Page transition videos should be added to the HotSpot.<br>
                   Intro Video field may be removed in future releases.
                </small>
            </div>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="form-group form-check">
                        <input type="checkbox" id="hide_from_menu"
                            v-model="page.hide_from_menu" class="form-check-input" />
                        <label for="hide_from_menu">Hide From Menu</label>
                        <small class="form-text" >
                            Hides the Page from the navigation menu
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="menu_display_order">Menu Display Order</label>
                        <input type="number" min="0" id="menu_display_order"
                            v-model="page.menu_display_order" class="form-control" />
                        <small class="form-text" >
                            Controls the order this page is displayed in the menu
                        </small>
                    </div>
                </div>
            </div>
        </div>

        </div><!-- end row -->
        <div class="btn-toolbar mt-2 pt-2">
            <div class="btn-group mr-4">
                <button type="button" class="btn btn-primary"
                    :disabled="isSaving"
                    @click="savePage">Save</button>
            </div>
            <div class="btn-group mr-auto">
                <slot name="cancel-link"></slot>
            </div>
        </div>
    </div><!-- end fluid-container -->
</template>

<script>
    export default {
      data(){
        return {
           newBackgroundImage: null,
           errors: [],
           isSaving: false,
        };
      },
      props: {
        page: Object,
        saveUrl: String,
        successUrl: String,
        otherPages: Array,
      },
      mounted() {
        console.log('Component mounted.')
      },
      methods: {
        onBackgroundImageChange(event) {
          console.log('On Background Image File Input Change:');
          let files = event.target.files;
          if(files.length > 0 ) {
            this.newBackgroundImage = files[0];
          } else {
            this.newBackgroundImage = null;
          }
        },
        async savePage() {
            console.log('Save Page Click');
            console.log(this.saveUrl);
            this.errors = [];
            this.isSaving = true;

            let formData = new FormData();
            
            let formFields = [
                'name', 'slug', 'minimum_age', 'html_content',
                'background_video', 'intro_video', 'parent_id',
                'hide_from_menu', 'menu_display_order'
            ];

            // Only add non-null field to FormData to keep it from sending "null" string and
            // causing server validation errors
            formFields.forEach( (dataKey) => {
                if(this.page[dataKey]) {
                   
                    formData.append(dataKey, this.page[dataKey]);
                } 
            });

            
            // Add the Background Image    
            if( this.newBackgroundImage ) {
                formData.append('background_image', this.newBackgroundImage);
            }

            try {
                const response = await axios.post(this.saveUrl, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                });
                // success - redirect - can I show a message ??
                console.log(response);
                if(response.data.success) {
                    //this.page = response.data.store_page;
                    window.location.href = this.successUrl;
                } else {
                    this.errors.push('Unknown Error Saving Page');
                }
            }
            catch(error) {
                let err_msg = 'Error Saving Store Page';
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
                this.isSaving = false;
            }
        }
      },
      computed: {
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
      max-width: 200px;
      max-height: 200px;
      margin: 10px 0;
      box-shadow: 2px 4px 4px rgba(#000, 0.2);
    }
  }
</style>
