<template>
    <div>
        <div class="d-flex align-items-center mb-4">
            <button class="btn btn-outline-primary rounded-pill" @click="$emit('cancel')">
                <app-icon name="arrow-left" class="size-16" />
                {{ $t("back") }}
            </button>
            <div class="h4 my-0 mx-3 text-muted">|</div>
            <div class="d-flex justify-content-between">
                <h5 class="m-0" v-if="selectedTemplate?.id">{{ $t("update_template") }}</h5>
                <h5 class="m-0" v-else>{{ $t("new_template") }}</h5>
            </div>
        </div>
        <div class="row">
            <app-overlay-loader v-if="preloader" />
            <form class="col-12 col-lg-7" :class="{ 'loading-opacity': preloader }">
                <div class="card card-body card-with-shadow p-3 border-0">
                    <div class="row">
                        <div class="col-12">
                            <label> {{ $t("template_name") }}</label>
                            <app-input v-model="formData.template_name" />
                            <small class="text-danger" v-if="errors.template_name">{{ errors.template_name[0] }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 p-3 my-3" style="background-color: var(--default-border-color);">
                            <span class="font-weight-bold"> {{ $t("department_details") }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3">
                            <label> {{ $t("field_1") }}</label>
                            <app-input v-model="formData.field1" />
                            <small class="text-danger" v-if="errors.field1">{{ errors.field1[0] }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3">
                            <label> {{ $t("field_2") }}</label>
                            <app-input v-model="formData.field2" />
                            <small class="text-danger" v-if="errors.field2">{{ errors.field2[0] }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3">
                            <label> {{ $t("field_3") }}</label>
                            <app-input v-model="formData.field3" />
                            <small class="text-danger" v-if="errors.field3">{{ errors.field3[0] }}</small>
                        </div>
                    </div>
                    <hr class="my-3 mx-0">
                    <div class="row">
                        <div class="col">
                            <div class="py-3 d-flex">
                                <button v-if="selectedTemplate?.id" class="mr-3 btn btn-success" type="button"
                                    @click="update()">
                                    {{ $t("Update") }}
                                </button>
                                <button v-else class="mr-3 btn btn-success" type="button" @click="submit()">
                                    {{ $t("save") }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import _ from 'lodash'
import { FormMixin } from "../../../../../../core/mixins/form/FormMixin";
export default {
    mixins: [FormMixin],
    props: ['selectedTemplate'],
    data() {
        return {
            preloader: false,
            formData: {
                template_name: '',
                field1: '',
                field2: '',
                field3: '',
            },
            errors: {}
        }
    },
    methods: {
        update() {
            if (this.isValid()) {
                new Promise((resolve, reject) => {
                    this.preloader = true
                    return resolve(this.formData)
                }).then(res => {
                    this.$emit('success', res)
                }).finally(() => {
                    this.preloader = false
                })
            }
        },
        submit() {
            if (this.isValid()) {
                new Promise((resolve, reject) => {
                    this.preloader = true
                    return resolve({id: Date.now(), ...this.formData})
                }).then(res => {
                    this.$emit('success', res)
                }).finally(() => {
                    this.preloader = false
                })
            }
        },
        isValid() {
            let isValidForm = true
            this.errors = {}
            for (let key of Object.keys(this.formData)) {
                if (!(!!this.formData[key])) {
                    this.errors[key] = [`${key} required.`]
                    isValidForm = false
                }
            }
            return isValidForm
        }
    },
    created() {
        if (!!this.selectedTemplate) {
            this.formData = _.cloneDeep(this.selectedTemplate)
        }
    }
}
</script>