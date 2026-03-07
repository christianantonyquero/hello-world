<template>
    <div class="store-song-edit">
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
                     <div v-if="song.track_audio_file" class="image-thumb-wrapper">
                        <label>Current Audio File</label>
                        <audio class="audio-player" controls :src="song.track_audio_file">
                        </audio>

                    </div>

                    <label for="track_audiofile">Change Audio File URL (.MP3)</label>
                    <input type="url" id="track_audiofile" 
                           class="form-control" 
                           v-model="song.track_audio_file"/>
                    <small class="form-text text-muted">URL to Audio File for Song</small>
                </div>
                
                <div class="form-group">
                    <div v-if="song.track_artwork" class="image-thumb-wrapper">
                        <label>Current Artwork</label>
                        <img :src="song.track_artwork_url" class="thumb" />
                    </div>

                    <label for="track_artwork">Change Artwork</label>
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
                    @click="updateSongClick">Save Song</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        saveUrl: String,
        successUrl: String,
        song: Object,
    },
    data() {
        return {
            errors: [],
            isProcessing: false,
            newArtwork: null,
            newAudioFile: null
        }
    },
    methods: {
        onTrackArtworkChange() {
            console.log('onTrackArtworkChange');
            console.log('onTrackAudioFileChange');
            let files = event.target.files;
            if (files.length > 0) {
                this.newArtwork = files[0];
            } else {
                this.newArtwork = null;
            }
        },
        async updateSongClick() {
            console.log('updateSongClick()');

            this.errors = [];
            this.isProcessing = true;

            // Build Form obj from song obj
            let formData = new FormData();
            let formFields = [
                'track_name', 'artist_name', 'album_name', 'play_order', 'track_audio_file'
            ];
            // Only add non-null field to FormData to keep it from sending "null" string and
            // causing server validation errors
            formFields.forEach( (dataKey) => {
                if(this.song[dataKey]) {
                    formData.append(dataKey, this.song[dataKey]);
                } 
            });

            // Add Files if changed
            if( this.newArtwork ) {
                formData.append('track_artwork', this.newArtwork);
            }

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

<style scoped lang="scss">
.image-thumb-wrapper {
    label {
      display: block;
    }
    img {
      display: block;
      max-width: 100px;
      max-height: 100px;
      margin: 10px 0;
      box-shadow: 2px 4px 4px rgba(#000, 0.2);
    }
}
.audio-player {
    border: 2px solid #444;
    padding: 10px;
}
</style>