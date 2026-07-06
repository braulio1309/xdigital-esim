<template>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-8">
                <div class="back-image"
                     :style="'background-image: url('+urlGenerator(configData.company_banner)+')'">
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 pl-md-0">
                <div class="login-form d-flex align-items-center">
                      <form class="sign-in-sign-up-form w-100"
                          ref="form"
                          :data-url="formUrl"
                          action="store">

                        <div class="text-center mb-4">
                            <img :src="urlGenerator(configData.company_logo)" alt=""
                                 class="img-fluid logo">
                        </div>
                        <div class="d-flex align-items-center justify-content-center flex-wrap partner-logos mb-4">
                            <img src="/images/logo.png" alt="Xcertus" class="partner-logo" />
                            <img src="/images/nomadesim.png" alt="NomadeSIM" class="partner-logo" />
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12 px-0">
                                <h6 class="text-center mb-0">{{ $t('hi_there') }}</h6>
                                <label class="text-center d-block">{{ $t('log_in_to_your_dashboard') }}</label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div v-if="!isTwoFactorStep" class="form-group col-12 px-0">
                                <label for="login_email">{{ $t('email') }}</label>
                                <app-input type="email"
                                           v-model="login.email"
                                           :placeholder="$t('enter_your_email')"
                                           :required="true"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div v-if="!isTwoFactorStep" class="form-group col-12 px-0">
                                <label for="login_password">{{ $t('password') }}</label>
                                <app-input type="password"
                                           v-model="login.password"
                                           :show-password="true"
                                           :placeholder="$t('enter_your_password')"
                                           :required="true"/>
                            </div>
                        </div>
                        <div class="form-row" v-if="isTwoFactorStep">
                            <div class="form-group col-12 px-0">
                                <div class="alert alert-info mb-3" role="alert">
                                    {{ twoFactorMessage }}
                                </div>
                                <label for="login_code">{{ $t('verification_code') }}</label>
                                <app-input type="text"
                                           v-model="login.code"
                                           :placeholder="$t('enter_verification_code')"
                                           :required="true"
                                           :max-length="4"
                                           :alphanumeric="true"
                                           :autocomplete="'one-time-code'"/>
                            </div>
                        </div>
                        <div class="form-row" v-if="recaptchaEnable == 1">
                            <div v-if="!isTwoFactorStep" class="form-group col-12 px-0">
                                <re-captcha :site-key="siteKey"></re-captcha>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12 px-0">
                                <app-load-more :preloader="preloader"
                                               :label="isTwoFactorStep ? $t('verify_code') : $t('login')"
                                               type="submit"
                                               class-name="btn btn-primary btn-block text-center"
                                               @submit="submit"/>
                            </div>
                        </div>
                        <div class="form-row" v-if="isTwoFactorStep">
                            <div class="form-group col-12 px-0 text-center">
                                <button type="button"
                                        class="btn btn-link p-0 text-decoration-none"
                                        @click="backToLogin">
                                    {{ $t('back_to_login') }}
                                </button>
                            </div>
                        </div>
                        <div
                            class="form-row form-row flex-column flex-md-row justify-content-center justify-content-md-between justify-content-lg-between">
                            <a :href="urlGenerator('/forget-password')"
                               class="bluish-text d-flex align-items-center justify-content-center justify-content-lg-end">
                                <app-icon name="lock" class="pr-2"/> {{ $t('forgot_password') }}
                            </a>
                            <a v-if="configData.registration === 'on'" :href="urlGenerator('/user/register')"
                               class="bluish-text d-flex align-items-center justify-content-center justify-content-lg-end">
                                <app-icon name="user" class="pr-2"/> {{ $t('register') }}
                            </a>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <p class="text-center mt-5">
                                    {{ $t('copyright_text') + configData.company_name }}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ThemeMixin from "../../../../core/mixins/global/ThemeMixin";
    import {AuthMixin} from "./Mixins/AuthMixin";
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        name: "Login",
        mixins: [AuthMixin, ThemeMixin],
        components: {},
        props: {
            siteKey: String,
            recaptchaEnable: {},
        },
        data() {
            return {
                urlGenerator,
                login: {email: '', password: '', code: ''},
                isTwoFactorStep: false,
                twoFactorMessage: '',
            };
        },
        computed: {
            formUrl() {
                return this.isTwoFactorStep ? '/admin/users/login/verify' : '/admin/users/login';
            }
        },
        methods: {
            submit() {
                this.save(this.isTwoFactorStep ? {code: this.login.code} : this.login);
            },
            afterSuccess(res) {
                if (res.data && res.data.two_factor_required) {
                    this.isTwoFactorStep = true;
                    this.twoFactorMessage = res.data.message;
                    this.login.code = '';
                    this.$toastr.s(res.data.message);
                    return;
                }

                window.location = res.data;
            },
            backToLogin() {
                this.isTwoFactorStep = false;
                this.twoFactorMessage = '';
                this.login.code = '';
            }
        }
    }
</script>

<style scoped>
.partner-logos {
    gap: 16px;
}
.partner-logo {
    max-height: 50px;
    max-width: 140px;
    object-fit: contain;
}
</style>
