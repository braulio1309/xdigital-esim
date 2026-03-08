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
                            title: 'Link de Referencia',
                            type: 'custom-html',
                            key: 'referral_link',
                            modifier: (value, row) => {
                                if (!row.codigo) {
                                    return '<span class="text-muted">Sin código</span>';
                                }
                                const slug = row.nombre.toLowerCase().replace(/\s+/g, '-');
                                const link = `${window.location.origin}/registro/esim/${slug}-${row.codigo}`;
                                return `
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control form-control-sm mr-2" value="${link}" readonly id="sp-link-${row.id}" style="max-width: 300px;">
                                        <button class="btn btn-sm btn-primary" data-sp-link-id="sp-link-${row.id}">
                                            <i class="mdi mdi-content-copy"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        },
                        {
                            title: 'Comisión %',
                            type: 'custom-html',
                            key: 'commission_percentage',
                            modifier: (value) => {
                                const pct = parseFloat(value || 0).toFixed(2);
                                return `<span class="badge badge-info">${pct}%</span>`;
                            }
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
                            title: this.$t('download_commissions'),
                            icon: 'download',
                            type: 'none',
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
                    showSearch: true,
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
                } else if (action.title === this.$t('download_commissions')) {
                    this.downloadCommissions(row);
                }
            },
            downloadCommissions(row) {
                window.location.href = urlGenerator(`super-partners/${row.id}/export-commissions`);
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
            },
            copyToClipboard(elementId) {
                const input = document.getElementById(elementId);
                if (input) {
                    const textToCopy = input.value;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            this.$toastr.s('Link copiado al portapapeles');
                        }).catch(() => {
                            this.fallbackCopy(input);
                        });
                    } else {
                        this.fallbackCopy(input);
                    }
                }
            },
            fallbackCopy(input) {
                input.select();
                input.setSelectionRange(0, 99999);
                try {
                    document.execCommand('copy');
                    this.$toastr.s('Link copiado al portapapeles');
                } catch (err) {
                    this.$toastr.e('Error al copiar el link');
                }
            }
        },
        mounted() {
            const self = this;
            $(document).on('click', '[data-sp-link-id]', function(e) {
                e.preventDefault();
                const linkId = $(this).data('sp-link-id');
                self.copyToClipboard(linkId);
            });
        },
        beforeDestroy() {
            $(document).off('click', '[data-sp-link-id]');
        }
    }
</script>
