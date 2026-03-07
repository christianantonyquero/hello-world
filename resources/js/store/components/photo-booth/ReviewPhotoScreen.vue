<template>
    <div class="photobooth-image-container">
        <img class="size-helper" src="/img/photobooth/photo-helper.jpg" width="500" height="500">
        <img :src="photo" class="review-photo"/>

        <div class="review-buttons">
            <button class="button is-light retake" @click="$emit('retakePhoto')">Retake</button>
            <button class="button is-primary user-photo" @click="$emit('usePhoto')">Use Photo</button>
        </div>
    </div>
</template>

<script>
export default {
    name: "PhotoBoothReviewPhotoScreen",
    props: {
        photo: {
            type: String,
            required: true,
        }
    }
}
</script>