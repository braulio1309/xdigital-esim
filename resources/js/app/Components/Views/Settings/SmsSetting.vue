<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else ref="form" data-url="/sms-settings"
          class="mb-0"
          :class="{'loading-opacity': preloader}">
        <div class="form-group row align-items-center">
            <label for="smsSettingsSmsDriver" class="col-lg-3 col-xl-3 mb-lg-0">
                {{ $t('sms_driver') }}
            </label>
            <div class="col-lg-8 col-xl-8">
                <app-input id="smsSettingsSmsDriver"
                           type="select"
                           v-model="smsSettings.sms_driver"
                           :required="true"
                           :list="driverList"/>
            </div>
        </div>

        <!--For Nexmo driver-->
        <fieldset v-if="smsSettings.sms_driver === 'vonage'">
            <div class="form-group row align-items-center">
                <label for="smsSettingsSmsSentFrom" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('sms_send_from_name_or_phone_number') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input
                        type="text"
                        v-model="smsSettings.sms_sent_from"
                        :placeholder="$t('type_a_name_or_number_you_want_to_sent_sms_from')"
                        :required="true"/>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label for="smsSettingsKey" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('api_key') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="smsSettingsKey"
                               type="text"
                               v-model="smsSettings.api_key"
                               :placeholder="$t('enter_vonage_api_key')"
                               :required="true"/>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label for="smsSettingsSecretKey" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('api_secret') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="smsSettingsSecretKey"
                               type="text"
                               v-model="smsSettings.api_secret"
                               :placeholder="$t('enter_vonage_api_secret')"
                               :required="true"/>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('test_number') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input type="tel-input"
                               v-model="smsSettings.test_number"
                               :placeholder="$t('type_test_number')"/>
                </div>
            </div>
        </fieldset>

        <!--For Twilio driver-->
        <fieldset v-if="smsSettings.sms_driver === 'twilio'">
            <div class="form-group row align-items-center">
                <label for="smsSettingsKey" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('account_sid') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="smsSettingsKey"
                               type="text"
                               v-model="smsSettings.account_sid"
                               :placeholder="$t('enter_account_sid')"
                               :required="true"/>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label for="smsSettingsSecretKey" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('auth_token') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="smsSettingsSecretKey"
                               type="text"
                               v-model="smsSettings.auth_token"
                               :placeholder="$t('enter_auth_token')"
                               :required="true"/>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label for="smsSettingsSecretKey" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('twilio_number') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="smsSettingsSecretKey"
                               type="text"
                               v-model="smsSettings.twilio_number"
                               :placeholder="$t('enter_twilio_number')"
                               :required="true"/>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label for="smsSettingsSecretKey" class="col-lg-3 col-xl-3 mb-lg-0">
                    {{ $t('twilio_test_number') }}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <app-input id="smsSettingsSecretKey"
                               type="text"
                               v-model="smsSettings.twilio_test_number"
                               :placeholder="$t('enter_twilio_test_number')"
                               :required="true"/>
                </div>
            </div>
        </fieldset>

        <div class="mt-5 action-buttons">
            <button class="btn btn-primary mr-2" @click.prevent="submit">
                {{ $t('save') }}
            </button>
        </div>
    </form>
</template>

<script>
    import {FormMixin} from "../../../../core/mixins/form/FormMixin";
    import * as actions from "../../../Config/ApiUrl";

    export default {

        name: "index.vue",
        mixins: [FormMixin],
        data() {
            return {
                preloader: false,
                smsSettings: {},
                driverList: [
                    {id: '', value: this.$t('select_driver')},
                    {id: 'vonage', value: "Vonage"},
                    {id : 'twilio' , value : 'Twilio'}
                ],
                showError: false,
            }
        },
        created() {
            this.getData();
        },
        methods: {

            beforeSubmit() {
                this.preloader = true;
            },
            submit() {
                this.save(this.smsSettings);
            },
            afterFinalResponse() {
                this.preloader = false;
            },
            afterSuccess(res) {
                this.$toastr.s(res.data.message);
            },
            afterError(res) {
                this.showError = true;
                this.$toastr.e(res.data.message);
            },
            getData() {
                this.preloader = true;
                let url = actions.GET_SMS_SETTINGS_INFO;
                this.axiosGet(url).then(response => {
                    if (response.data) this.smsSettings = response.data;
                }).catch(({response}) => {

                }).finally(() => {
                    this.preloader = false;
                });
            }
        }
    }
</script>
