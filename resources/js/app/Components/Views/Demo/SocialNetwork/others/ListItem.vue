<template>
    <a class="btn btn-link text-left px-0 w-100" :class="isActive(url)" :href="url" :target="target">
        <div class="d-flex align-items-center gap-x-1">
            <app-icon v-if="icon" :name="icon" :size="fontSize"/>
            <div class="text-capitalize" :style="`font-size: ${fontSize}px !important`">{{ title }}</div>
        </div>
    </a>
</template>

<script>
export default {
    props: {
        url: {
            type: String,
            default: '#'
        },
        icon: {
            type: String,
            default: ''
        },
        title: {
            type: String,
            default: ''
        },
        target: {
            type: String,
            default: ''
        },
        fontSize: {
            type: Number,
            default: 14
        }
    },
    methods: {
        isActive(url) {
            return window.location.href === url ? 'text-primary' : ''
        },
    }
}
</script>

<style scoped>

</style>