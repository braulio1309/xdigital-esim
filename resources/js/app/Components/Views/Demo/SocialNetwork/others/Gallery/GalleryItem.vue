<template>
    <a
        :href="item"
        :data-pswp-width="w"
        :data-pswp-height="h"
        target="_blank" rel="noreferrer"
        class="d-inline-block align-items-center d-flex position-relative"
        :class="grid ? 'preview preview--grid': index === 0 ? 'preview' : 'preview preview--sm'">
        <img v-if="type === 'image'" :src="item" style="max-width: 100%; width:100%; height: 100%; max-height: 100%; object-fit: cover;" alt="">
        <video v-if="type === 'video'" height="100%"><source :src="item" type="video/mp4"></video>
        <slot></slot>
    </a>
</template>

<script>
export default {
    props: ['item','index', 'type', 'grid'],
    data() {
        return {
            w: 0,
            h: 0
        }
    },
    methods: {
        getMeta(url) {
            let img = new Image();
            let _this = this
            img.onload = function () {
                _this.w = this.width
                _this.h = this.height
            };
            img.src = url;
        }
    },
    mounted() {
        if(this.type === 'image') {
            this.getMeta(this.item)
        }else if(this.type === 'video') {
            console.log('video', this.item)
        }
    }
}
</script>

<style scoped lang="scss">
.preview {
    width: 100%;
    height: 315px;
    object-fit: cover;
    overflow: hidden;
    border-radius: 15px;
    &--sm {
        width: 32.2%;
        height: 120px;
    }

    &--grid {
        width: 33.02% !important;
        //height: auto;
        //height: 120px;
    }
}

@media (max-width: 768px) {
    .preview{
        &--grid{
            height: 120px;
        }
    }
}
</style>