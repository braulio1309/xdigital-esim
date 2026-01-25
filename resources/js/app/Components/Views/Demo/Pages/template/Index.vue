<template>
    <div class="content-wrapper">
        <div class="position-relative" :class="{ 'loading-opacity': preloader }">
            <app-overlay-loader v-if="preloader" />
            <template-list v-if="!isAddTemplate" :list="templateList" :name-key="'template_name'" @add-new="addNew"
                :actions="actions" />
            <add-edit-form v-else @success="afterSuccess" @cancel="cancelAddEditForm"
                :selectedTemplate="selectedTemplate" />
            <app-confirmation-modal v-if="isDeleteConfirmationModal" icon="trash-2" modal-id="team-delete-confirmation-modal" @confirmed="deleteTemplate" @cancelled="close" />
        </div>
    </div>
</template>

<script>
import _ from 'lodash'
import AddEditForm from "./TemplateForm.vue"
import TemplateList from "./TemplateList.vue"

export default {
    components: { TemplateList, AddEditForm },
    data() {
        return {
            preloader: true,
            isAddTemplate: false,
            errors: {},
            templateList: [],
            selectedTemplate: null,
            isDeleteConfirmationModal: false,
            actions: [
                {
                    id: 1,
                    title: "Edit template",
                    method: (template) => {
                        this.selectedTemplate = template
                        this.isAddTemplate = true;
                    },
                },
                {
                    id: 2, title: "Delete template", method: (template) => {
                        this.selectedTemplate = template
                        this.isDeleteConfirmationModal = true
                    }
                },
                {
                    id: 3, title: "Copy template", method: (template) => {
                        let copyTemplate = _.cloneDeep(template);
                        copyTemplate.template_name = ''
                        delete copyTemplate.id
                        this.selectedTemplate = copyTemplate
                        this.isAddTemplate = true;
                    }
                },
            ]
        };
    },
    methods: {
        addNew() {
            this.isAddTemplate = true;
        },
        cancelAddEditForm() {
            this.isAddTemplate = false;
            this.selectedTemplate = null;
        },
        getTemplateList(response) {
            this.preloader = true
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    resolve(response)
                }, 500)
            }).then(res => {
                this.templateList = res
            })
                .finally(() => {
                    this.preloader = false
                })
        },
        afterSuccess(template) {
            this.cancelAddEditForm()
            let index = this.templateList.findIndex(t => t.id === template.id);
            if (index !== -1) {
                let templates = _.cloneDeep(this.templateList)
                templates.splice(index, 1, template)
                this.getTemplateList(templates)
            } else {
                this.getTemplateList([...this.templateList, template])
            }
        },
        deleteTemplate() {
            let index = this.templateList.findIndex(t => t.id === this.selectedTemplate.id)
            if (index !== -1) {
                let templates = _.cloneDeep(this.templateList)
                templates.splice(index, 1)
                this.getTemplateList(templates)
            }
        },
        close() {
            this.selectedTemplate = null
            this.isDeleteConfirmationModal = false
        },
    },
    mounted() {
        this.getTemplateList([])
    }
};
</script>

<style scoped lang="scss">
.document-massage {
    background-color: rgba(0, 123, 255, 0.2);
    border-radius: 8px;
}
</style>
