<template>
    <div :id="galleryID">
        <div class="d-flex flex-wrap" style="gap: 5px;">
            <template v-for="(item, index) in images">
                <gallery-item v-if="showMoreImage || (!showMoreImage && index < 4) || grid" :item="item" :index="index"
                    :type="getType(item)" :key="`item-${galleryID}-${index}`" :grid="grid">
                    <template v-if="!grid">
                        <div v-if="images.length > 4 && !showMoreImage && index === 3"
                            class="d-flex justify-content-center position-absolute w-100">
                            <button type="button" @click.prevent="showMoreImage = !showMoreImage" class="preview--btn">
                                +{{ images.length - 4 }}
                            </button>
                        </div>
                    </template>
                </gallery-item>
            </template>
        </div>
    </div>
</template>

<script>
import PhotoSwipe from 'photoswipe';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';
import GalleryItem from "./GalleryItem";

export default {
    components: { GalleryItem },
    props: {
        galleryID: String,
        images: Array,
        grid: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            showMoreImage: false,
        }
    },
    methods: {
        getType(url) {
            let images = ['png', 'jpg', 'webp', 'jpeg', 'gif']
            let videos = ['mp4', 'avi']
            let ext = url.split('.').pop().toLowerCase()
            return images.includes(ext) ? 'image' : videos.includes(ext) ? 'video' : 'file'
        }
    },
    mounted() {
        if (!this.lightbox) {
            this.lightbox = new PhotoSwipeLightbox({
                gallery: '#' + this.$props.galleryID,
                children: 'a',
                // need to imrpove. subfolder not work.
                // pswpModule: () => import('photoswipe'),
                pswpModule: PhotoSwipe,
                bgOpacity: 0.9,
            });
            this.lightbox.init();
        }
    },
    unmounted() {
        if (this.lightbox) {
            this.lightbox.destroy();
            this.lightbox = null;
        }
    },
};
</script>
<style scoped lang="scss">
.preview {
    width: 100%;
    height: 315px;
    object-fit: cover;
    overflow: hidden;

    &--sm {
        width: 32.6%;
        height: 120px;
    }

    &--btn {
        padding: 0 10px;
        color: #fff;
        background: #00000050;
        border-radius: 15px;
        border: none;

        &:hover {
            background: #00000070;
        }
    }
}
</style>
