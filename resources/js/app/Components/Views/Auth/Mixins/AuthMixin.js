import {FormMixin} from "../../../../../core/mixins/form/FormMixin";

export const AuthMixin ={
    props:{
        configData:{
            type: Object
        }
    },
    mixins: [FormMixin],
    computed: {
        authConfig() {
            return {
                company_banner: 'banner/69b32bc38517e.png',
                company_logo: 'logo/69b32c47b0fba.png',
                company_name: '',
                registration: 'off',
                ...(this.configData || {}),
            };
        }
    },
    data(){
        return{
            preloader: false,
        }
    },
    methods:{
        beforeSubmit() {
            this.preloader = true;
        },
        afterError(res) {
            this.preloader = false;
            this.$toastr.e(res.data.message);
        },
        afterFinalResponse(){
            this.preloader = false;
        }
    }
}
