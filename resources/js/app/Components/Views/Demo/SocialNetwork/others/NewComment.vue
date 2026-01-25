<template>
    <form action="" @submit.prevent="submitComment">
        <div class="d-flex align-items-center gap-x-1">
            <div class="form-group w-100 m-0">
                <input v-model="formData.message" type="text" class="form-control form-control-rounded"
                       name="content" maxlength="500" autocomplete="off" :placeholder="$t('comment')" required
                       @focus="focused = true" @blur="focused = false"
                >
            </div>
            <button class="btn btn-primary rounded-circle p-0 d-flex justify-content-center align-items-center" style="width: 50px; height: 44px;" type="submit" :disabled="!formData.message || submitting">
                <app-pre-loader v-if="submitting" spinnerClass="spinner-border-sm" />
                <span v-else>             
                    <app-icon name="send"/>
                </span>
            </button>
        </div>
        <div class="px-3 text-right text-muted" v-if="focused">
            <small>Total {{ this.formData.message.length }} out of 500</small>
        </div>
    </form>
</template>

<script>
export default {
    props: ['id', 'submitting'],
    data() {
        return {
            formData: {
                message: ''
            },
            focused: false
        }
    },
    methods: {
        submitComment(event) {
            let formData = event
            if (!this.submitting) {
                this.$emit('submit', formData)
            }
        }
    },
    watch: {
        'submitting': {
            handler: function (v) {
                if (!v) {
                    this.formData.message = ''
                }
            }
        }
    }
}
</script>

<style scoped lang="scss">
.form-control-rounded {
    border-radius: 25px !important;
    padding-right: 46px !important;
}

.form-control-button {
    // margin-left: -42px;
    background-color: #4466F210;
    border: none;
    height: 39px;
    width: 39px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;

    &:hover {
        background-color: #4466F240;
    }

    &--active {
        background-color: #4466F250;
    }
}

//.custom-from-group {
//    padding-right: 40px ;
//}
</style>