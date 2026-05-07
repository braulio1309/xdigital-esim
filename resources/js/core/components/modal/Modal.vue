<template>
    <div class="modal fade custom-scrollbar"
         :style="'cursor: url('+cursorImage()+'), auto !important'"
         :class="[modalSize === 'fullscreen' ? 'border-0 p-0 m-0' : '']"
         :data-backdrop="modalBackdrop ? modalBackdrop : 'static'"
         :data-keyboard="`${modalBackdrop}`"
         :id="modalId"
         tabindex="-1"
         role="dialog"
         aria-hidden="true">
        <div class="modal-dialog"
             :class="[modalAlignment === 'top' ? 'modal-dialog-top' : 'modal-dialog-centered', checkModalSize,{'modal-dialog-scrollable':modalScroll}]"
             role="document">
            <div class="modal-content">
                <div v-if="$slots.header" :key="modalId+'header'" class="modal-header">
                    <slot name="header"></slot>
                </div>

                <div v-if="$slots.body" :key="modalId+'body'" class="modal-body custom-scrollbar" :class="modalBodyClass">
                    <slot name="body"></slot>
                </div>

                <div v-if="$slots.footer" :key="modalId+'footer'" class="modal-footer">
                    <slot name="footer"></slot>
                </div>
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import AppFunction from "../../helpers/app/AppFunction";

export default {
    name: "AppModal",
    props: {
        modalId: {
            type: String,
            required: true
        },
        modalSize: {
            type: String,
            default: 'default'
        },
        modalAlignment: {
            type: String,
            default: 'top'
        },
        modalScroll: {
            type: Boolean,
            default: true
        },
        modalBodyClass: {
            type: String
        },
        modalBackdrop: {
            type: Boolean,
            default: true
        }
    },
    mounted() {
        this.$modalElement = $('#' + this.modalId);

        this.handleShown = () => {
            $('html').css('overflow-y', 'hidden');
        };

        this.handleHidden = () => {
            $('html').css('overflow-y', 'auto');
            this.cleanupModalArtifacts();
            this.$emit('close-modal', false);
        };

        this.$modalElement.on('shown.bs.modal', this.handleShown);
        this.$modalElement.on('hidden.bs.modal', this.handleHidden);

        this.$modalElement.modal('show');
    },
    beforeDestroy() {
        if (this.$modalElement) {
            this.$modalElement.off('shown.bs.modal', this.handleShown);
            this.$modalElement.off('hidden.bs.modal', this.handleHidden);

            if (this.$modalElement.hasClass('show')) {
                this.$modalElement.modal('hide');
            }
        }

        this.cleanupModalArtifacts();
    },
    computed: {
        checkModalSize() {
            if (this.modalSize === 'default') return "modal-default";
            else if (this.modalSize === 'small') return "modal-sm";
            else if (this.modalSize === 'large') return "modal-lg";
            else if (this.modalSize === 'extra-large') return "modal-xl";
            else if (this.modalSize === 'full-width') return "modal-fullwidth";
            else if (this.modalSize === 'fullscreen') return "full-screen-modal-dialog";
            else return '';
        }
    },
    methods: {
        cleanupModalArtifacts() {
            if (!$('.modal.show').length) {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $('body').css('padding-right', '');
                $('html').css('overflow-y', 'auto');
            }
        },
        cursorImage(){
            if (this.$store.state.theme.darkMode) {
                return AppFunction.getAppUrl('images/close-cursor-dark.png');
            } else {
                return AppFunction.getAppUrl('images/close-cursor-light.png');
            }
        }
    }
}
</script>
