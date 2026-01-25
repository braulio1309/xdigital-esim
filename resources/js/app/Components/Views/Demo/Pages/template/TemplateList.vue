<template>
    <div class="template">
        <button class="d-flex template__item shadow btn btn-primary justify-content-center align-items-center" @click="addNew">
            <div class="d-flex flex-column justify-content-center align-items-center">
                <app-icon name="plus" class="my-2" />
                <div class="flex-fill d-flex justify-content-center align-items-center">{{ $t('new_template') }}</div>
            </div>
        </button>
        <div class="d-flex flex-column template__item shadow" v-for="(template, index) in list" :key="`template-${index}`">
            <div class="flex-fill d-flex justify-content-center align-items-center">{{ template[nameKey] }}</div>
            <span v-if="template.is_default" class="badge badge-pill badge-sm badge-light text-primary" style="position: absolute; top: 10px; right: 10px;">{{ $t('default') }}</span>
            <div class="">
                <div class="d-flex justify-content-end border-top py-2">
                    <div class="btn-group">
                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button v-for="(action, index) in actions" :key="`action-${index}`" class="dropdown-item" type="button" @click="action.method(template)">{{ action.title }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        list: {
            type: Array,
            required: true
        },
        nameKey: {
            type: String,
            required: false,
            default: 'name',
        },
        actions: {
            type: Array,
            required: true
        },
    },
    data() {
        return {}
    },
    methods: {
        addNew() {
            this.$emit('add-new')
        }
    }
}
</script>

<style scoped lang="scss">
.template{
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    &__item{
        width: 172px;
        height: 220px;
        position: relative;
        &:hover{
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.2) !important;
        }
    }
}
</style>