<template>
    <div class="general-setting">
        <app-overlay-loader v-if="preloader"/>
        <form v-else ref="form" :data-url="STORAGE_SETTING" enctype="multipart/form-data"
              class="mb-0"
              :class="{'loading-opacity': preloader}">

            <!-- Storage Type -->
            <div class="form-group row align-items-center">
                <label for="storageSetting" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('storage_type') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="storageSetting"
                               type="select"
                               v-model="formData.storage_type"
                               :list="storageTypes"
                               :error-message="$errorMessage(errors, 'storage_type')"
                               :required="true"/>
                </div>
            </div>

            <fieldset v-if="formData.storage_type === 's3'">
                <div class="form-group row align-items-center">
                    <label for="aws_access_key" class="col-lg-3 col-xl-3 mb-lg-0">
                        {{ $t("aws_access_key") }}
                    </label>
                    <div class="col-lg-8 col-xl-8">
                        <app-input
                            id="aws_access_key"
                            type="text"
                            v-model="formData.aws_access_key"
                            :placeholder="$t('aws_access_key')"
                            :error-message="$errorMessage(errors, 'aws_access_key')"
                        />
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="aws_secret_key" class="col-lg-3 col-xl-3 mb-lg-0">
                        {{ $t("aws_secret_key") }}
                    </label>
                    <div class="col-lg-8 col-xl-8">
                        <app-input
                            id="aws_secret_key"
                            type="text"
                            v-model="formData.aws_secret_key"
                            :placeholder="$t('aws_secret_key')"
                            :error-message="$errorMessage(errors, 'aws_secret_key')"
                        />
                    </div>
                </div>
                <div class="form-group row align-items-center">
                    <label for="aws_region" class="col-lg-3 col-xl-3 mb-lg-0">
                        {{ $t("aws_region") }}
                    </label>
                    <div class="col-lg-8 col-xl-8">
                        <app-input
                            id="aws_region"
                            type="text"
                            v-model="formData.aws_region"
                            :placeholder="$t('aws_region')"
                            :error-message="$errorMessage(errors, 'aws_region')"
                        />
                    </div>
                </div>
                <div class="form-group row align-items-center">
                    <label for="aws_region" class="col-lg-3 col-xl-3 mb-lg-0">
                        {{ $t("aws_bucket_name") }}
                    </label>
                    <div class="col-lg-8 col-xl-8">
                        <app-input
                            id="aws_bucket"
                            type="text"
                            v-model="formData.aws_bucket_name"
                            :placeholder="$t('aws_bucket_name')"
                            :error-message="$errorMessage(errors, 'aws_bucket_name')"
                        />
                    </div>
                </div>
            </fieldset>

            <div class="mt-5 action-buttons">
                <button class="btn btn-primary mr-2" @click.prevent="submit">
                    {{ $t('save') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import {FormMixin} from "../../../../../core/mixins/form/FormMixin";
import {axiosGet} from "../../../../Helpers/AxiosHelper";
import {STORAGE_SETTING} from "../../../../Config/ApiUrl";

export default {
    name: "Index",
    mixins: [FormMixin],
    data() {
        return {
            errors: [],
            STORAGE_SETTING,
            preloader: false,
            storageTypes: [
                {id: 'public', value: 'System Storage'},
                // {id: 'local', value: 'Local'},
                {id: 's3', value: 'Amazon S3'},
            ],
            formData: {
                storage_type: 'public'
            }
        }
    },
    created() {
        this.getSettings();
    },
    methods: {
        getSettings() {
            axiosGet(STORAGE_SETTING).then(response => {
                if (Object.keys(response.data).length > 0){
                    this.formData = response.data
                }
            });
        },
        beforeSubmit() {
            this.preloader = true;
        },
        submit() {
            let formData = new FormData;
            for (const [key, value] of Object.entries(this.formData)) {
                formData.append(key, value);
            }
            this.save(formData);
        },
        afterSuccess(res) {
            this.$toastr.s(res.data.message);
            window.location.reload();
        },
        afterFinalResponse() {
            this.preloader = false;
        },
        afterError(response) {
            if (response.data.errors) {
                this.errors = response.data.errors;
            } else {
                this.$toastr.e(response.data.message);
            }
        },
    }
}
</script>
