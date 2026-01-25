<template>
    <div class="content-wrapper">

        <app-breadcrumb :page-title="$t('payment_method')" :directory="$t('sample_pages')" :icon="'copy'"/>

        <div class="card card-with-shadow border-0">

            <div class="card-header p-primary bg-transparent">
                <h5 class="mb-0"> Payment methods</h5>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="mb-0">Stripe</p>
                    <a v-if="isSetStripe" href="/stripe" class="btn btn-primary">Pay</a>
                    <app-note v-else content-type="html"
                              :show-title="false"
                              notes="Configure your stripe setting first!"/>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="mb-0">Paypal</p>
                    <a href="/create-payment" v-if="isSetPaypal" class="btn btn-primary">Pay</a>
                    <app-note v-else content-type="html"
                              :show-title="false"
                              notes="Configure your paypal setting first!"/>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="mb-0">Razorpay</p>
                    <a href="" @click.prevent="razorpayPay" v-if="isSetRazorpay" class="btn btn-primary">Pay</a>
                    <app-note v-else content-type="html"
                              :show-title="false"
                              notes="Configure your razorpay setting first!"/>
                </div>

            </div>

        </div>
    </div>
</template>
<script>
import {FormMixin} from "../../../../../core/mixins/form/FormMixin";
import {axiosGet, axiosPost,urlGenerator} from "../../../../Helpers/AxiosHelper";
import {
    GET_STRIPE_STATUS,
    GET_PAYPAL_STATUS,
    GET_RAZORPAY_STATUS,
    GET_RAZORPAY_INFORMATION
} from "../../../../Config/ApiUrl";

export default {
    name: "PaymentMethodView",
    mixins: [FormMixin],
    props: {
        message: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            isSetStripe: false,
            isSetPaypal: false,
            isSetRazorpay: false,
            razorPayKeyId : ''
        }
    },
    created() {
        this.getStripeStatus();
        this.getPaypalStatus();
        this.getRazorpayStatus();
        this.getRazorpayInformation();
        this.successMessage();

    },
    methods: {
        getStripeStatus() {
            axiosGet(GET_STRIPE_STATUS).then(({data}) => {
                this.isSetStripe = data.isSetStripe
            });
        },
        getPaypalStatus() {
            axiosGet(GET_PAYPAL_STATUS).then(({data}) => {
                this.isSetPaypal = data.isSetPaypal
            });
        },
        getRazorpayStatus() {
            axiosGet(GET_RAZORPAY_STATUS).then(({data}) => {
                this.isSetRazorpay = data.isSetRazorpay
            });
        },
        getRazorpayInformation() {
            axiosGet(GET_RAZORPAY_INFORMATION).then(({data}) => {
                this.razorPayKeyId = data.key_id
            });
        },
        razorpayPay() {
            let options = {
                "key": this.razorPayKeyId,
                "amount": 1000,
                "currency": "INR",
                "name": window.user ? window.user.full_name : '',
                "image": urlGenerator(window.settings.company_logo),
                "handler": ((response) => {
                    this.preloader = true;
                    axiosPost('razor-payment', {
                        razorpay_payment_id: response.razorpay_payment_id,
                        amount: 1000,
                        payment_type: 'razorpay',
                        invoice_id: 1
                    }).then((response) => {
                        this.$toastr.s(response.data.message);
                        this.preloader = false;
                    });
                }),
                "theme": {
                    "color": "#4466F2"
                }
            };
            const rozarpay = new Razorpay(options);
            rozarpay.on('payment.failed', (response) => {
                alert(response.error.reason);
            });
            rozarpay.open()
        },
        successMessage() {
            this.message ? this.$toastr.s(this.message) : '';
        }

    }
}
</script>
