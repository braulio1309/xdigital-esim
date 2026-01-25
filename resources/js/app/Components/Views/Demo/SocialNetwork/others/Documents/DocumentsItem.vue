<template>
    <div class="">
        <a :href="document" target="_blank">{{ truncateedText(document) }}</a>
    </div>
</template>

<script>

import {textTruncate} from "../../../../../../Helpers/Helpers";

export default {
    props: ['document'],
    methods: {
        truncateedText(url) {
            return textTruncate(url.split('.').at(-2).split('/').pop(), 50) + '.' + url.split('.').at(-1)
        }
    }
}
</script>

<style scoped>
</style>