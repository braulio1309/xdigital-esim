<template>
    <modal :modal-id="userAndRoles.users.createModalId"
           :title="$t('create_user')"
           :preloader="preloader"
           :modal-scroll="false"
           @submit="submit"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form ref="form" :data-url="actions.CREATE_USER"
                  :class="{'loading-opacity': preloader}">
                <div class="form-group row align-items-center">
                    <label for="firstName" class="col-sm-3 mb-0">
                        {{ $t('first_name') }}
                    </label>
                    <app-input id="firstName"
                               class="col-sm-9"
                               type="text"
                               v-model="user.first_name"
                               :placeholder="$t('enter_first_name')"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center">
                    <label for="lastName" class="col-sm-3 mb-0">
                        {{ $t('last_name') }}
                    </label>
                    <app-input id="lastName"
                               class="col-sm-9"
                               type="text"
                               v-model="user.last_name"
                               :placeholder="$t('enter_last_name')"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center">
                    <label for="userEmail" class="col-sm-3 mb-0">
                        {{ $t('email') }}
                    </label>
                    <app-input id="userEmail"
                               class="col-sm-9"
                               type="email"
                               v-model="user.email"
                               :placeholder="$t('enter_user_email')"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="userPassword" class="col-sm-3 mb-0">
                        {{ $t('password') }}
                    </label>
                    <app-input id="userPassword"
                               class="col-sm-9"
                               type="password"
                               v-model="user.password"
                               :placeholder="$t('enter_password')"
                               :required="true"/>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin';
    import {ModalMixin} from '../../../../Mixins/ModalMixin';
    import {UserAndRoleMixin} from '../Mixins/UserAndRoleMixin';
    import * as actions from '../../../../Config/ApiUrl';

    export default {
        name: "UserCreateModal",
        mixins: [FormMixin, ModalMixin, UserAndRoleMixin],
        data() {
            return {
                actions,
                user: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                },
            }
        },
        methods: {
            submit() {
                this.save(this.user);
            },

            afterSuccess(res) {
                this.$toastr.s(res.data.message);
                this.reLoadTable();
            },
        }
    }
</script>
