<template>
    <div class="d-flex flex-column" style="gap: 5px;">
        <template v-for="(document, index) in documents">
            <documents-item v-if="showMoreDocument || (!showMoreDocument && index < 4)" :document="document" :key="index" />
            <button v-if="documents.length > 4 && !showMoreDocument && index===3" class="btn btn-link text-primary"
                    @click.prevent="showMoreDocument = !showMoreDocument">+{{ documents.length - 4 }} more</button>
        </template>
    </div>
</template>

<script>
import DocumentsItem from "./DocumentsItem";
export default {
    props: ['documents'],
    components: { DocumentsItem },
    data() {
        return {
            showMoreDocument: false,
        }
    }
}
</script>

<style scoped>

</style>