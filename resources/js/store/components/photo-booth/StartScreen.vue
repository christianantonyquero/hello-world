<template>
    <div class="photobooth-image-container">
        <img class="size-helper" src="/img/photobooth/photo-helper.jpg" width="500" height="500">
        <section class="start-screen container-content">

            <button type="button" class="open-camera"
                @click="$emit('openCamera')">Open Camera</button>

            <div class="instructions" key="instructions">
                <p>To use this feature you must allow access to your camera.</p>
            </div>
            
        </section> <!-- .start-screen -->
    </div>
</template>

<script>
export default {
    name: 'PhotoBoothStartScreen',
}
</script>