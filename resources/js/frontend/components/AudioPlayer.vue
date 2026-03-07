<template>
    <div class="audio-player">
        <div class="audio-player-background"></div>
        <div class="current-track" v-if="current_song">
            <div v-if="current_song.track_artwork" class="artwork"
                 :style="{ backgroundImage: `url('${current_song.track_artwork_url}')` }" >
            </div>
            <div class="track-info">
                <span class="track">{{current_song.track_name}}</span>
                <span class="artist">{{current_song.artist_name}}</span>
                <span v-if="current_song.album_name" class="album">{{current_song.album_name}}</span>
            </div>
        </div>
        <div class="track-progress">
            <div class="track-progress-bar-wrapper"
                 @click="clickProgressBar">
                <div class="track-progress-bar"
                     :class="{ 'no-transition': remove_progress_bar_animation }"
                     :style="{ width: current_track_progress_percentage + '%'}"></div>
            </div>
            <div class="track-time-wrapper">
                <span class="start-time">{{current_track_time | time_format }}</span>
                <span class="duration">{{track_duration | time_format}}</span>
            </div>
        </div>

        <div class="player-controls">
            <!-- previous -->
            <div class="button btn-track btn-track-prev" @click="previous_song" v-if="num_songs > 1">
                <!--<svg width="25" height="25" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M4 5h3v10H4V5zm12 0v10l-9-5 9-5z"/></svg>-->
            </div>

            <div class="button btn-play-pause" @click="playPauseToggleClick">
                <template v-if="is_playing">
                    <!-- pause icon -->
                    <span class="is_playing"></span>
                    <!--<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M5 4h3v12H5V4zm7 0h3v12h-3V4z"/></svg>-->
                </template>
                <template v-else>
                    <span class="is_paused"></span>
                    <!-- play icon -->
                    <!-- <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6z"/></svg> -->
                </template>
            </div>
            <!-- next -->
            <div class="btn btn-track btn-track-next" @click="next_song" v-if="num_songs > 1">
                <!-- <svg width="25" height="25" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 5h3v10h-3V5zM4 5l9 5-9 5V5z"/></svg> -->
            </div>
        </div>


        <div class="navigation-tip"></div>
    </div>
</template>

<script>

import round  from 'lodash/round';

export default {
    props: {
        song_list: Array,
        dark_mode: {
            type: Boolean,
            default: false,
        }
    },
    data: function() {
        return {
            song_idx: 0,
            error_msg: null,
            audio: null,
            is_playing: false,
            remove_progress_bar_animation: false,
            current_track_progress_percentage: 0,
            current_track_time: 0,
            track_duration: 0,
        
        }
    },
    created() {
        console.log('Audio Player Comonent Created');
    },
    mounted() {
        console.log('Audio Player Comonent Mounted');
    },
    methods: {
        playPauseToggleClick() {
            // Button click to toggle playing or pausting the music
            if(this.is_playing) {
                this.pause();
            } else {
                this.play();
            }
        },
        next_song() {
            // Looping index for next song
            console.log('next song click');
            this.song_idx = (++this.song_idx) % this.song_list.length;

            this.changeSong();

        },
        previous_song() {
            console.log('previous song click');
            // Looping index for previous song
            this.song_idx--;
            if(this.song_idx < 0) {
                this.song_idx = this.song_list.length - 1;
            }

            this.changeSong();
        },
        changeSong() {
            this.remove_progress_bar_animation = true;

            let was_playing = this.is_playing;


            this.current_track_time = 0;
            this.current_track_progress_percentage = 0;
            
            this.pause();
            this.audio = new Audio(this.current_song.track_audio_file);

            // Setup Event Listeners for new audio;
            this.audio.addEventListener('loadedmetadata', this.onAudioMetaDataLoaded);
            this.audio.addEventListener('ended', this.onSongEnded);

            if(was_playing) {
                this.play();
            }

            // Delay a short time before turning the progress bar animation back on to allow 
            // the progress bar to redraw at 0%
            setTimeout( () => {
                this.remove_progress_bar_animation = false;
            }, 100);
            
        },
        play() {
            if(this.audio) {
                if(!this.is_playing) {
                    this.is_playing = true;
                    this.updateTrackTime(true);
                    this.audio.play();
                } 
            }
        },
        pause() {
            if(this.audio) {
                this.audio.pause();
                this.is_playing = false;
            }
            clearTimeout(this.checkCurrentPositionInTrack);
        },
        onSongEnded(){
            console.log('onSongEnded');
            
            // Play the next song
            this.next_song();
        },
        onAudioMetaDataLoaded(event){
            console.log('loadedmetadata');
            this.track_duration = Math.round(this.audio.duration);
        },
        updateTrackTime(startStopToggle) {
            var self = this;
            this.checkCurrentPositionInTrack = setTimeout(
                function() {
                    self.current_track_time = self.audio.currentTime;
                    self.current_track_progress_percentage = (self.audio.currentTime / self.audio.duration) * 100;
                    self.updateTrackTime(true);
                }.bind(this), 1000);
        },
        clickProgressBar(event) {
            if(this.audio && this.audio.duration) {
                
                // Find the x position inside the progress bar
                let xPercent = (event.offsetX / event.currentTarget.offsetWidth);
                
                if(xPercent < 0) {
                    xPercent = 0;
                } else if(xPercent > 100) {
                    xPercent = 100;
                }
               
                let newTime = round(xPercent * this.audio.duration, 4);
                let oldTime = this.audio.currentTime;
               
                // Jump to the timestamp of the audio based on the percentage clicked
                this.audio.currentTime = newTime;
            }  
        },
    },
    computed: {
        current_song() {
           return this.song_list[this.song_idx];
        },
        num_songs() {
            return this.song_list.length;
        }
    },
    watch: {
        song_list: function(new_song_list, old_song_list) {
            console.log('Song List Watcher Fired');
            if(this.song_list.length > 0) {
                this.changeSong(0);
                this.audio.loop = false;
            }
        },
        // Alway round the current time - not sure why it was done this way
        current_track_time: function() {
			this.current_track_time = Math.round(this.current_track_time);
		}
    },
    filters: {
		time_format: function(s) {
			return (s - (s %= 60)) / 60 + (9 < s ? ":" : ":0") + s;
		}
    },
    beforeDestroy: function() {
        this.audio.removeEventListener("ended", this.handleEnded);
		this.audio.removeEventListener("loadedmetadata", this.handleEnded);
		clearTimeout(this.checkCurrentPositionInTrack);
    }
}
</script>
