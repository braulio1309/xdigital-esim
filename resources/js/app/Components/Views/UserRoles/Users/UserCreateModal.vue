<template>
    <modal :modal-id="userAndRoles.users.createModalId"
           :title="$t('create_user')"
           :preloader="preloader"
           :modal-scroll="false"
           @submit="submit"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form ref="form" data-url="/app/user-list"
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
                <div class="form-group row align-items-center">
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
                <div class="form-group row align-items-center">
                    <label for="userType" class="col-sm-3 mb-0">
                        {{ $t('user_type') }}
                    </label>
                    <app-input id="userType"
                               class="col-sm-9"
                               type="select"
                               :list="userTypeOptions"
                               list-value-field="name"
                               v-model="user.user_type"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center mb-0" v-if="user.user_type">
                    <label for="roles" class="col-sm-3 mb-0">
                        {{ $t('role') }}
                    </label>
                    <app-input id="roles"
                               class="col-sm-9"
                               type="multi-select"
                               :list="roleLists"
                               list-value-field="name"
                               :isAnimatedDropdown="true"
                               v-model="roles"
                               :required="true"/>
                </div>
                <!-- Additional fields for Beneficiario -->
                <div v-if="user.user_type === 'beneficiario'">
                    <div class="form-group row align-items-center mt-3">
                        <label for="beneficiarioNombre" class="col-sm-3 mb-0">
                            {{ $t('nombre') }}
                        </label>
                        <app-input id="beneficiarioNombre"
                                   class="col-sm-9"
                                   type="text"
                                   v-model="user.beneficiario_nombre"
                                   :placeholder="$t('enter_nombre')"
                                   :required="true"/>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="beneficiarioDescripcion" class="col-sm-3 mb-0">
                            {{ $t('descripcion') }}
                        </label>
                        <app-input id="beneficiarioDescripcion"
                                   class="col-sm-9"
                                   type="textarea"
                                   v-model="user.beneficiario_descripcion"
                                   :placeholder="$t('enter_descripcion')"
                                   :required="true"/>
                    </div>
                </div>
                <!-- Additional fields for Cliente -->
                <div v-if="user.user_type === 'cliente'">
                    <div class="form-group row align-items-center mt-3">
                        <label for="clienteNombre" class="col-sm-3 mb-0">
                            {{ $t('nombre') }}
                        </label>
                        <app-input id="clienteNombre"
                                   class="col-sm-9"
                                   type="text"
                                   v-model="user.cliente_nombre"
                                   :placeholder="$t('enter_nombre')"
                                   :required="true"/>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="clienteApellido" class="col-sm-3 mb-0">
                            {{ $t('apellido') }}
                        </label>
                        <app-input id="clienteApellido"
                                   class="col-sm-9"
                                   type="text"
                                   v-model="user.cliente_apellido"
                                   :placeholder="$t('enter_apellido')"
                                   :required="true"/>
                    </div>
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
                user: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    user_type: '',
                    beneficiario_nombre: '',
                    beneficiario_descripcion: '',
                    cliente_nombre: '',
                    cliente_apellido: ''
                },
                roles: [],
                roleLists: [],
                userTypeOptions: [
                    {id: 'admin', name: this.$t('admin')},
                    {id: 'beneficiario', name: this.$t('beneficiario')},
                    {id: 'cliente', name: this.$t('cliente')}
                ]
            }
        },
        created() {
            this.getRoles();
        },
        methods: {
            submit() {
                this.user.roles = this.roles;
                this.save(this.user);
            },

            afterSuccess(res) {
                this.$toastr.s(res.data.message);
                this.reLoadTable();
            },

            getRoles() {
                let url = actions.ROLES;

                this.axiosGet(url).then(response => {
                    this.roleLists = response.data.data;
                }).catch(({response}) => {
                    // Handle error
                }).finally(() => {
                    this.preloader = false;
                });
            }
        }
    }
</script>
