<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="'Super Partners'" :directory="'Super Partners'" :icon="'star'"/>
            </div>
            <div class="col-sm-12 col-md-6 breadcrumb-side-button">
                <div class="float-md-right mb-3 mb-sm-3 mb-md-0">
                    <button type="button"
                            class="btn btn-primary btn-with-shadow"
                            @click="openAddEditModal">
                        {{ $t('add') }}
                    </button>
                </div>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="getListAction"/>

        <add-modal v-if="isAddEditModalActive"
                   :table-id="tableId"
                   :selected-url="selectedUrl"
                   @close-modal="closeAddEditModal"/>

        <app-delete-modal v-if="deleteConfirmationModalActive"
                          :preloader="deleteLoader"
                          modal-id="super-partner-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    import {urlGenerator} from "../../../../Helpers/AxiosHelper";

    import AddModal from "./AddModal";

    export default {
        extends: CoreLibrary,
        name: "SuperPartnersList",
        components: {
            AddModal
        },
        data() {
            return {
                deleteLoader: false,
                isAddEditModalActive: false,
                deleteConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'super-partners-table',
                rowData: {},
                options: {
                    url: actions.SUPER_PARTNERS,
                    name: 'Super Partners',
                    datatableWrapper: false,
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('nombre'),
                            type: 'text',
                            key: 'nombre',
                        },
                        {
                            title: this.$t('descripcion'),
                            type: 'text',
                            key: 'descripcion',
                        },
                        {
                            title: 'Código',
                            type: 'text',
                            key: 'codigo',
                        },
                        {
                            title: this.$t('action'),
                            type: 'action',
                            key: 'invoice',
                            isVisible: true
                        }
                    ],
                    actions: [
                        {
                            title: this.$t('edit'),
                            icon: 'edit',
                            type: 'none',
                            component: 'app-add-modal',
                            modalId: 'super-partner-add-edit-modal',
                        },
                        {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'super-partner-delete',
                        }
                    ],
                    showFilter: false,
                    showSearch: false,
                    paginationType: "pagination",
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "default",
                }
            }
        },
        methods: {
            openAddEditModal() {
                this.isAddEditModalActive = true;
            },
            closeAddEditModal() {
                this.isAddEditModalActive = false;
                this.selectedUrl = '';
            },
            getListAction(row, action) {
                this.rowData = row;
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `super-partners/${row.id}`;
                    this.isAddEditModalActive = true;
                } else if (action.title === this.$t('delete')) {
                    this.deleteConfirmationModalActive = true;
                }
            },
            confirmed() {
                this.deleteLoader = true;
                this.axiosDelete(`super-partners/${this.rowData.id}`)
                    .then(response => {
                        this.$toastr.s(response.data.message);
                        this.$hub.$emit('reload-' + this.tableId);
                    }).catch(error => {
                        this.$toastr.e(error.response.data.message);
                    }).finally(() => {
                        this.deleteLoader = false;
                        this.deleteConfirmationModalActive = false;
                    });
            },
            cancelled() {
                this.deleteConfirmationModalActive = false;
            }
        }
    }
</script>
