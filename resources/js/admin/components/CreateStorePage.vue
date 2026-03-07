<template>
    <div class="store-page-create card">
        <div v-if="errors" class="col-12 mt-2">
            <errors :errors="errors"></errors>
        </div>
        <div class="row">
            <div class="col-sm-7">
                <div class="form-group">
                    <label for="page_name">Page Name</label>
                    <input type="text" class="form-control" id="page_name" v-model="page.name"
                        aria-describedby="page name" placeholder="Page Name" required autocomplete="off" />
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" v-model="page.slug"
                        aria-describedby="slug" placeholder="slug" autocomplete="off" required />
                </div>

                <div class="form-group">
                    <label for="parent">Parent Page</label>

                    <select class="form-control" id="parent"
                        v-model="page.parent_id">
                        <option selected :value="null">-- No Parent --</option>
                        <option
                            v-for="page in allPages"
                            :key="page.id"
                            :value="page.id">{{page.name}}</option>
                    </select>
                </div>

                <!-- hide we are not using it 
                <div class="form-group">
                    <label for="minimum_age">Minumum Age</label>
                    <input type="number" class="form-control" id="minimum_age" v-model="page.minimum_age"
                        autocomplete="off" aria-describedby="minimum_age" />
                </div>
                -->
                <div class="form-group">

                    <label for="html_content">Page Content (HTML)</label>
                    <textarea id="html_content" v-model="page.html_content" class="form-control" rows="10"
                        placeholder="Add HTML content to the page"></textarea>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="form-group">
                    <label for="background_image">Background Image</label>
                    <input type="file" id="background_image" @change="onBackgroundImageChange"
                        class="form-control-file" placeholder="URL for a background video" />
                </div>
                
                <div class="form-group">
                    <label for="background_video">Background Video</label>
                    <input type="url" id="background_video" v-model="page.background_video" class="form-control"
                        placeholder="URL for a background video" />
                </div>


                <div class="form-group">
                    <label for="intro_video">Intro Video</label>
                    <input type="url" id="intro_video" v-model="page.intro_video" class="form-control"
                        placeholder="URL for an intro video" />
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

            </div><!-- end col -->
        </div><!-- end row -->
        <div class="btn-toolbar mt-2 pt-2">

            <div class="btn-group mr-4">
                <button type="button" class="btn btn-primary"
                        :disabled="isSaving"
                        @click.prevent="createPage">Save</button>
            </div>
            <div class="btn-group mr-auto">
                <slot name="cancel-link"></slot>
            </div>
        </div>
    </div>
</template>

<script>
    import slugify from "slugify";

    export default {
        data() {
            return {
                page: {

                    name: null,
                    slug: null,
                    minimum_age: null,
                    page_content: null,
                    background_video: null,
                    intro_video: null,
                    background_image: null,
                    parent_id: null,
                    hide_from_menu: false,
                    menu_display_order: 10,
                },
                errors: [],
                isSaving: false,
            };
        },
        props: {
            saveUrl: String,
            successUrl: String,

            allPages: Array,
        },
        mounted() {
            console.log("CreateStore Component mounted.");
        },
        methods: {
            onBackgroundImageChange(event) {
                console.log("On Background Image File Input Change:");
                let files = event.target.files;
                if (files.length > 0) {
                    this.page.background_image = files[0];
                } else {
                    this.page.background_image = null;
                }
            },
            async createPage() {
                console.log('Create Page Click');
                this.errors = [];

                this.isSaving = true;

                // Build Form obj from page obj
                let formData = new FormData();
                Object.keys(this.page).forEach( (key) => {
                    let value = this.page[key];
                    if( value ) {
                        console.log(key, value);
                        formData.append(key, value);
                    }
                });

                // Submit data
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
                    console.log(error);
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
        watch: {
            "page.name"(newName, oldName) {
                this.page.slug = slugify(newName, {lower:true, strict: true});
            } 
        }
    };

</script>

