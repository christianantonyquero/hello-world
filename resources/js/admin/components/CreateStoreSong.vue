<template>
    <div class="store-song-create">
        <div v-if="errors" class="col-12 mt-2">
            <errors :errors="errors"></errors>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="track_name">Track Name</label>
                    <input type="text" class="form-control" id="track_name" v-model="song.track_name"
                           required autocomplete="off" />
                </div>

                <div class="form-group">
                    <label for="artist_name">Artist Name</label>
                    <input type="text" class="form-control" id="artist_name" v-model="song.artist_name"
                           required autocomplete="off" />
                </div>

                <div class="form-group">
                    <label for="album_name">Album Name</label>
                    <input type="text" class="form-control" id="album_name" v-model="song.album_name"
                           autocomplete="off" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-group">
                    <label for="play_order">Play Order</label>
                    <input type="number" min="1" max="100" class="form-control" id="play_order" v-model="song.play_order"
                           required autocomplete="off" />
                </div>

                 <div class="form-group">
                   <label for="track_audiofile">Audio File URL (.MP3)</label>
                   <input type="url" id="track_audiofile" 
                           class="form-control" 
                           v-model="song.track_audio_file"/>
                    <small class="form-text text-muted">URL to Audio File for Song</small>
                </div>

                <div class="form-group">
                    <label for="track_artwork">Artwork</label>
                    <input type="file" id="track_artwork" @change="onTrackArtworkChange"
                        accept="image/png, image/jpeg"
                        class="form-control-file" />
                </div>

               

            </div>
        </div><!-- end row -->
        <div class="row mt-2 pt-2">
            <slot name="cancel-link"></slot>
            <button type="button" class="btn-primary"
                    :disabled="isProcessing"
                    @click="createSongClick">Create Song</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        saveUrl: String,
        successUrl: String,
    },
    data() {
        return {
            song: {
                track_name: null,
                artist_name: null,
                album_name: null,
                play_order: 10,
                track_artwork: null,
                track_audio_file: null,
            },
            errors: [],
            isProcessing: false
        }
    },
    methods: {
        onTrackArtworkChange() {
            console.log('onTrackArtworkChange');
            console.log('onTrackAudioFileChange');
            let files = event.target.files;
            if (files.length > 0) {
                this.song.track_artwork = files[0];
            } else {
                this.song.track_artwork = null;
            }
        },
        async createSongClick() {
            console.log('createSongClick()');
            this.errors = [];
            this.isProcessing = true;

            // Build Form obj from song obj
            let formData = new FormData();
            Object.keys(this.song).forEach( (key) => {
                let value = this.song[key];
                if( value ) {
                    formData.append(key, value);
                }
            });

            // Submit
            try {
                const response = await axios.post(this.saveUrl, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                });
                // success - redirect
                console.log(response);
                if(response.data.success) {
                    window.location.href = this.successUrl;
                } else {
                    this.errors.push('Unknown Error Saving Song');
                }
            }
            catch(error) {
                let err_msg = 'Error Saving Song';
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
                this.isProcessing = false;
            }
        }
    }
}
</script>