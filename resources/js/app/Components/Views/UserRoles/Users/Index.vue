<template>
    <div class="card card-with-shadow border-0 pb-primary">
        <div class="card-header d-flex align-items-center p-primary primary-card-color">
            <h5 class="card-title d-inline-block mb-0">{{ $t('users') }}</h5>
            <app-search @input="getSearchValue"/>
        </div>
        <div class="p-primary d-flex align-items-center primary-card-color">
            <ul class="nav tab-filter-menu justify-content-flex-end">
                <li class="nav-item" v-for="(item, index) in userFilterOptions" :key="index">
                    <a href="#"
                       class="nav-link py-0 font-size-default"
                       :class="[value == item.id ? 'active' : index === 0 && value === '' ? 'active': '']"
                       @click="getFilterValue(item.id)">
                        {{ item.translated_name }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body px-primary">
            <div class="table-responsive">
                <app-table :id="data.tableId"
                           class="remove-datatable-x-padding"
                           :options="scopedUserTableOptions"
                           :filtered-data="filteredData"
                           :search="search"
                           @action="action"/>
            </div>
        </div>
    </div>
</template>

<script>
    import {TableMixin} from '../Mixins/TableMixin';
    import * as actions from '../../../../Config/ApiUrl';

    export default {
        name: "User",
        mixins: [TableMixin],
        data() {
            return {
                value: '',
                filteredData: {},
                userTableOptions: {
                    name: 'Users',
                    url: actions.USERS,
                    tablePaddingClass: 'pt-0',
                    datatableWrapper: false,
                    showHeader: false,
                    tableShadow: false,
                    columns: [
                        {
                            title: this.$t('user'),
                            type: 'media-object',
                            key: 'profile_picture',
                            imgKey: "image",
                            mediaTitleKey: 'full_name',
                            mediaSubtitleKey: 'email',
                            default: "",
                            isVisible: true,
                            modifier:(value, row)=>{
                                return row.profile_picture ? row.profile_picture.full_url : '';
                            }
                        },
                        {
                            title: 'Tipo',
                            type: 'text',
                            key: 'user_type_display',
                            isVisible: true,
                            modifier: (value) => {
                                const text = (value || '').toString();
                                const roleText = (text || 'N/A')
                                    .replace(/admin_partner/gi, 'Directivo Super Partner')
                                    .replace(/admin_beneficiario/gi, 'admin_partner')
                                    .replace(/super_partner/gi, 'Super Partner')
                                    .replace(/partner/gi, 'Partner')
                                    .replace(/atencion_cliente/gi, 'Atencion al cliente')
                                    .replace(/directivo/gi, 'Directivo');

                                const partner = row.affiliated_beneficiario && row.affiliated_beneficiario.nombre
                                    ? `Partner: ${row.affiliated_beneficiario.nombre}`
                                    : '';
                                const superPartner = row.affiliated_super_partner && row.affiliated_super_partner.nombre
                                    ? `Super Partner: ${row.affiliated_super_partner.nombre}`
                                    : '';

                                const belongsTo = partner && superPartner
                                    ? `${partner} | ${superPartner}`
                                    : (partner || superPartner || 'N/A');

                                return `${roleText} - ${belongsTo}`;
                            }
                        },
                        {
                            title: this.$t('status'),
                            type: 'custom-html',
                            key: 'status',
                            isVisible: true,
                            modifier: (value) => {
                                return `<span class="badge badge-sm badge-pill badge-${value.class}">${value.translated_name}</span>`;
                            }
                        },
                        {
                            title: this.$t('action'),
                            type: 'action',
                            key: 'invoice',
                            isVisible: true
                        },
                    ],
                    showSearch: false,
                    showFilter: false,
                    paginationType: 'pagination',
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "default",
                    actions: [
                        {
                            title: this.$t('edit'),
                            icon: 'edit',
                            type: 'none',
                        },
                        {
                            title: this.$t('active'),
                            icon: 'check-circle',
                            type: 'none',
                            modifier: (row) => {
                                const {status} = row;
                                return status.name != "status_invited" && status.name != "status_active" ? true : false;
                            }
                        },
                        {
                            title: this.$t('de_activate'),
                            icon: 'x-circle',
                            type: 'none',
                            modifier: (row) => {
                                const {status} = row;
                                return status.name != "status_invited" && status.name != "status_inactive" ? true :false;
                            }
                        },
                        {
                            title: this.$t('delete'),
                            icon: 'trash-2',
                            type: 'none',
                        },
                    ],
                },
                userFilterOptions: [
                    {id: '', name: 'all_users', translated_name: 'All Users'},
                    {id: 1, name: 'active', translated_name: 'Active'},
                    {id: 2, name: 'inactive', translated_name: 'Inactive'},
                ],
            }
        },
        computed: {
            loggedInUser() {
                return this.$store.state.user && this.$store.state.user.loggedInUser
                    ? this.$store.state.user.loggedInUser
                    : null;
            },
            isScopedViewer() {
                const userType = this.loggedInUser ? this.loggedInUser.user_type : null;

                return ['super_partner', 'admin_partner', 'beneficiario', 'admin_beneficiario'].includes(userType);
            },
            scopedUserTableOptions() {
                if (!this.isScopedViewer) {
                    return this.userTableOptions;
                }

                return {
                    ...this.userTableOptions,
                    columns: this.userTableOptions.columns.filter(column => column.type !== 'action'),
                    actions: [],
                    showAction: false,
                };
            },
        },
        methods: {
            getFilterValue(item) {
                this.value = item;
                this.filteredData['status-id'] = item;
                this.$hub.$emit('reload-' + this.data.tableId);
            }
        }
    }
</script>

<style scoped>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .table-responsive {
        display: block;
        width: 100%;
    }
}
</style>
