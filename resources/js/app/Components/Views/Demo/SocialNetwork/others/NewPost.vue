<template>
    <div class="card card-body rounded-corner" :class="editData?.id ? 'p-0 border-0' : ''">
        <form class="d-flex flex-column py-2 gap-y-1" method="POST" ref="form" :data-url="''">
            <!-- body input -->
            <vue-editor v-model="formData.body" :id="`post-${editData?.id}` || 'new-post'" :editorToolbar="[]"
                @text-change="typeChange" :placeholder="$t('share_ideas')" :disabled="loadingData" />

            <!-- show images list -->
            <div v-if="!!formData.images.length">
                <hr class="post-separator">
                <div class="d-flex flex-wrap" style="gap: 5px;">
                    <template v-for="(image, index) in formData.images">
                        <div v-if="showAllImages || (!showAllImages && index < 4)" :key="`image-item-${index}`"
                            class="preview d-inline-block align-items-center d-flex position-relative"
                            :class="{ 'preview--sm': index !== 0 }">
                            <div class="w-100 h-100" v-html="getImageContent(image)"></div>
                            <div v-if="formData.images.length > 4 && !showAllImages && index === 3"
                                class="d-flex justify-content-center position-absolute w-100">
                                <button type="button" @click.prevent="showAllImages = !showAllImages"
                                    class="preview--btn">+{{ formData.images.length - 4 }}</button>
                            </div>
                            <!-- remove current file -->
                            <button type="button" class="fab fab-red fab-transparent fab-2 position-absolute"
                                style="right: 10px; top: 10px; z-index: 100;" @click="removeImage(index, image)">
                                <app-icon name="x" :size="16" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- show video previews list -->
            <div v-if="previews.length > 0">
                <hr class="post-separator">
                <div class="d-flex flex-wrap" style="gap: 5px;">
                    <template v-for="(preview, index) in previews">
                        <div v-if="showAllPreviews || (!showAllPreviews && index < 1)" :key="`preview-item-${index}`"
                            :class="index === 0 ? 'preview' : 'preview preview--sm'"
                            class="d-inline-block align-items-center d-flex position-relative">
                            <div class="w-100 h-100" v-html="preview"></div>
                            <!-- remove current file -->
                            <button type="button" class="fab fab-red fab-transparent fab-2 position-absolute"
                                style="right: 10px; top: 10px; z-index: 100;" @click="removePreviews(index, preview)">
                                <app-icon name="x" :size="16" />
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" class="btn btn-link text-primary p-0 pt-3" v-if="previews.length > 1">
                    <span :key="'show_preview_button_' + showAllPreviews" class="d-flex align-items-center gap-x-1"
                        @click.prevent="showAllPreviews = !showAllPreviews">
                        <app-icon :name="showAllPreviews ? 'eye-off' : 'eye'" :size="18"
                            :key="'show_preview_button_' + showAllPreviews" />
                        <span>{{ showAllPreviews ? $t('hide') : $t('show_all_previews') }}</span>
                    </span>
                </button>
            </div>

            <!-- show documents list -->
            <div v-if="!!formData.documents.length">
                <hr class="post-separator">
                <div class="d-flex flex-wrap align-items-center" style="gap: 5px;">
                    <template v-for="(document, index) in formData.documents">
                        <div v-if="showMoreDocument || (!showMoreDocument && index < 4)" :key="`document-item-${index}`"
                            class="d-flex align-items-center badge badge-pill gap-x-1 text-primary badge-document">
                            <div v-html="getDocumentContent(document)"></div>
                            <!-- remove current file -->
                            <button type="button" class="fab fab-red fab-transparent fab-2"
                                @click="removeDocument(index, document)">
                                <app-icon name="x" :size="16" />
                            </button>
                        </div>
                        <button v-if="formData.documents.length > 4 && !showMoreDocument && index === 3"
                            class="btn btn-link text-primary" @click.prevent="showMoreDocument = !showMoreDocument">+{{
                                formData.documents.length - 4 }} more</button>
                    </template>
                </div>
            </div>

            <!-- polls -->
            <div v-if="showPollLayout">
                <hr class="post-separator">
                <div class="d-flex flex-column gap-y-1 mb-3">
                    <small v-if="formData.poll?.options?.length" class="text-muted">{{ $t('poll_options') }}</small>

                    <div class="d-flex gap-x-1" v-for="(poll, index) in formData.poll?.options"
                        :key="`poll-options-${index}`">
                        <div class="flex-fill">
                            <input class="form-control" type="text" :ref="'poll_option_' + index"
                                :placeholder="$t('poll_option')" v-model="poll.body" @keyup.enter="addOption">
                            <!--                            <app-input type="text" :ref="index" :placeholder="$t('poll_option')" v-model="poll.body" @keyup.enter="addOption"/>-->
                        </div>
                        <button type="button" class="btn p-0 btn-clear text-danger" @click="removePollOption(index)">
                            <app-icon name="x" :size="16" />
                        </button>
                    </div>
                </div>
                <div class="d-flex gap-x-2 align-items-center">
                    <button type="button" class="btn text-primary px-0 btn-sm" style="box-shadow: none !important;"
                        @click="addOption">
                        <app-icon name="plus" :size="16" />
                        {{ $t('add_option') }}
                    </button>
                    <div class="d-flex align-items-center gap-x-1">
                        <input type="checkbox" v-model="formData.poll.multiple_answerable">
                        <span class="">{{ $t('multiple_answerable') }} </span>
                    </div>
                    <div class="d-flex align-items-center gap-x-1">
                        <input type="checkbox" v-model="formData.poll.is_addable">
                        <span class="">{{ $t('option_addable') }} </span>
                    </div>
                </div>
            </div>

            <hr class="post-separator">

            <!-- emoji -->
            <div class="d-flex align-items-center justify-content-between gap-x-3">
                <div class="d-flex gap-x-2 align-items-center flex-fill">
                    <emoji-picker @emoji="insert" :search="search">
                        <div slot="emoji-invoker" slot-scope="{ events: { click: clickEvent } }" @click.stop="clickEvent">
                            <button type="button" class="btn btn-link text-primary p-0" :disabled="loadingData"
                                title="Emoji">
                                <span class="d-flex align-items-center gap-x-1">
                                    <app-icon name="smile" :size="18" />
                                    <!--                                    <span class="">{{ $t('emoji') }}</span>-->
                                </span>
                            </button>
                        </div>
                        <div class="position-absolute card card-body p-2" style="width: 260px; z-index: 100;"
                            slot="emoji-picker" slot-scope="{ emojis, insert }">
                            <div>
                                <div>
                                    <input class="form-control-sm w-100" type="text" v-model="search" />
                                </div>
                                <div class="custom-scrollbar" style="height: 220px; overflow-y: scroll;">
                                    <div v-for="(emojiGroup, category) in emojis" :key="category">
                                        <small>{{ category }}</small>
                                        <div>
                                            <span class="cursor-pointer" style="font-size: 20px;"
                                                v-for="(emoji, emojiName) in emojiGroup" :key="emojiName"
                                                @click="insert(emoji)" :title="emojiName">
                                                {{ emoji }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </emoji-picker>

                    <!-- add video button -->
                    <!--                    <button type="button" class="btn btn-link p-0"-->
                    <!--                            @click.prevent="addVideo'.mp4, .avi')" :disabled="loadingData" >-->
                    <!--                        <span class="d-flex align-items-center gap-x-1">-->
                    <!--                            <app-icon name="film" :size="18"/>-->
                    <!--                            <span>Video</span>-->
                    <!--                        </span>-->
                    <!--                    </button>-->

                    <!-- add image button -->
                    <button type="button" class="btn btn-link text-primary p-0"
                        :title="`File type: ${imagesType.toString()}, File size: 1MB`" @click.prevent="addImage(imagesType)"
                        :disabled="loadingData">
                        <span class="d-flex align-items-center gap-x-1">
                            <app-icon name="image" :size="18" />
                            <!--                            <span>{{ $t('image') }}</span>-->
                        </span>
                    </button>
                    <button type="button" class="btn btn-link text-primary p-0"
                        :title="`File type: ${documentsType.toString()}, File size: 1MB`"
                        @click.prevent="addDocument(documentsType)" :disabled="loadingData">
                        <span class="d-flex align-items-center gap-x-1">
                            <app-icon name="file-plus" :size="18" />
                            <!--                            <span>{{ $t('file') }}</span>-->
                        </span>
                    </button>

                    <!-- add poll button -->
                    <button type="button" class="btn btn-sm px-3 rounded-pill"
                        :class="{ active: showPollLayout, 'btn-outline-danger': showPollLayout, 'btn-outline-primary': !showPollLayout }"
                        @click="pollLayoutTrigger" :disabled="loadingData">
                        <span class="d-flex align-items-center gap-x-1">
                            <app-icon name="list" :size="18" />
                            <span>{{ showPollLayout ? $t('cancel_poll') : $t('create_poll') }}</span>
                        </span>
                    </button>
                </div>
                <div>
                    <select class="form-control" v-model="formData.shared_with">
                        <option value="public" selected>{{ $t('public') }}</option>
                        <option value="team">{{ $t('team') }}</option>
                    </select>
                </div>
            </div>

            <!-- share with public/team -->
            <div v-if="formData.shared_with === 'team'">
                <app-input type="multi-select" :list="teams" v-model="formData.teams" :disabled="loadingData"
                    list-value-field="name" />
            </div>

            <!-- submit button -->
            <div v-if="isFocus">
                <button class="btn btn-primary shadow-none btn-block rounded-pill" type="button" :disabled="loadingData"
                    style="box-shadow: none !important;" @click.prevent="submit">
                    <app-icon name="send" :size="14" class="mr-2" />
                    {{ editData?.id ? $t('update') : $t('post') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import EmojiPicker from 'vue-emoji-picker'
import extractUrls from "extract-urls"
import { VueEditor } from "vue2-editor";
import { textTruncate } from "../../../../../Helpers/Helpers";

import { urlGenerator } from "../../../../../Helpers/AxiosHelper";
import FormHelperMixins from "../../../../../Mixins/global/FormHelperMixins";

export default {
    mixins: [FormHelperMixins],
    props: {
        editData: {},
        nofetch: {
            type: Boolean,
            default: false
        }
    },
    components: {
        EmojiPicker,
        VueEditor
    },
    data() {
        return {
            urlGenerator,
            formData: {
                body: '',
                shared_with: 'public',
                images: [],
                remove_images: [],
                documents: [],
                remove_documents: [],
                poll: {
                    options: [],
                    is_addable: 0,
                    multiple_answerable: 0
                },
                teams: [],
            },
            previews: [],
            previewUrls: [],
            maxFileSize: (1024 * 1024), // 1Mb
            search: '',
            showPollLayout: false,
            showAllImages: false,
            showMoreDocument: false,
            showAllPreviews: false,
            customToolbar: [],
            imagesType: ['png', 'gif', 'jpg', 'jpeg', 'webp'],
            documentsType: ['pdf', 'doc', 'docx', 'csv', 'xlsx'],
            teams: [],
            loadingData: false
        }
    },
    computed: {
        isFocus() {
            return this.formData.body || this.formData.images.length || this.formData.documents.length || this.formData.poll?.options.length
        }
    },
    methods: {
        async typeChange() {
            let urls = extractUrls(this.formData.body) || []
            for (let url of urls) {
                if (!this.previewUrls.includes(url)) {
                    let content = await this.getHtmlContent(url)
                    if (content) {
                        this.previewUrls.push(url)
                        this.previews.push(content)
                    }
                }
            }
        },
        // poll functions
        pollLayoutTrigger() {
            this.showPollLayout = !this.showPollLayout
            if (!this.showPollLayout) {
                this.formData.poll.options = this.formData.poll.options.filter(i => i.id)
            }

            if (this.showPollLayout && this.formData.poll.options.length === 0) {
                this.addOption();
            }
        },
        addOption() {
            this.formData.poll.options.push({ body: '' })
            this.$nextTick(function () {
                let element = this.$refs['poll_option_' + (this.formData.poll.options.length - 1).toString()]
                if (element[0]) {
                    element[0].focus()
                }
            })
        },
        removePollOption(index) {
            this.formData.poll.options.splice(index, 1)
        },

        // images
        addImage(extensions) {
            let input = document.createElement('input')
            input.type = 'file'
            input.multiple = true
            input.accept = extensions.map(i => `.${i}`)
            input.click();
            input.addEventListener('change', (event) => {
                let files = event.target.files
                for (let file of files) {
                    if (file.size < this.maxFileSize) {
                        this.formData.images.push(file)
                    } else {
                        this.$toastr.e(this.$t('file_size_exceeded'))
                    }
                }
            })
        },
        removeImage(index, item) {
            if (!(item instanceof File)) {
                this.formData.remove_images.push(JSON.parse(JSON.stringify(item)))
            }
            this.formData.images.splice(index, 1);
        },
        getImageContent(data) {
            let isFile = data instanceof File
            let isImageUrl = (url) => ['png', 'gif', 'jpg', 'jpeg', 'webp'].includes(url.split('.').pop().toLowerCase())

            if (!isFile) {
                if (isImageUrl(data.full_url)) return `<img src="${data.full_url}" style="width: 100%;" alt="">`
            } else {
                let fileType = (data) => {
                    if (data.type.split('/')[0] === 'image') return 'image'
                }
                let url = URL.createObjectURL(data)
                if (fileType(data) === 'image') {
                    return `<img src="${url}" style="max-width: 100%; width:100%; height: 100%; max-height: 100%; object-fit: cover;" alt="">`
                }
            }
        },

        // documents
        addDocument(extensions) {
            let input = document.createElement('input')
            input.type = 'file'
            input.multiple = true
            input.accept = extensions.map(i => `.${i}`)
            input.click();
            input.addEventListener('change', (event) => {
                let files = event.target.files
                for (let file of files) {
                    if (file.size < this.maxFileSize) {
                        this.formData.documents.push(file)
                    } else {
                        this.$toastr.e(this.$t('file_size_exceeded'))
                    }
                }
            })
        },
        removeDocument(index, item) {
            if (!(item instanceof File)) {
                this.formData.remove_documents.push(JSON.parse(JSON.stringify(item)))
            }
            this.formData.documents.splice(index, 1);
        },
        getDocumentContent(data) {
            let isFile = data instanceof File
            let isDocumentUrl = (url) => this.documentsType.includes(url.split('.').pop().toLowerCase())

            let truncatedText = url => textTruncate(url.split('.').at(-2).split('/').pop(), 25) + '.' + url.split('.').at(-1)

            if (!isFile) {
                if (isDocumentUrl(data.full_url)) return `<span>${truncatedText(data.full_url)}</span>`
            } else {
                let fileType = (data) => {
                    return this.documentsType.includes(data.type.split('/')[1])
                }
                if (fileType(data)) {
                    return `<span>${truncatedText(data.name)}</span>`
                }
            }
        },

        //  emoji functions
        emoji(e) {
            let keyboardEvent = new KeyboardEvent('keypress', { key: ' ', ctrlKey: true, metaKey: true })
            document.dispatchEvent(keyboardEvent);
        },
        insert(emoji) {
            this.formData.body += emoji
        },

        // preview functions
        async getHtmlContent(data) {
            let isFile = data instanceof File
            let isImageUrl = (url) => ['png', 'gif', 'jpg', 'jpeg', 'webp'].includes(url.split('.').pop().toLowerCase())

            let isValidYoutubeUrl = (url) => {
                let p = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                if (url.match(p)) {
                    return url.match(p)[1];
                }
                return false;
            }

            let isValidVimeoUrl = (url) => {
                let p = /(http|https)?:\/\/(www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/;
                if (url.match(p)) {
                    return url.match(p)[1];
                }
                return false;
            }

            if (!isFile) {
                if (isImageUrl(data)) return `<img src="${data}" style="width: 100%;" alt="">`
                else if (isValidYoutubeUrl(data)) {
                    function getId(url) {
                        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                        let match = url.match(regExp);
                        if (match && match[2].length === 11) {
                            return match[2];
                        } else {
                            return 'error';
                        }
                    }
                    const videoId = getId(data);
                    return '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe>';
                } else if (isValidVimeoUrl(data)) {
                    return await axios.get(`https://vimeo.com/api/oembed.json?url=${data}`).then(({ data }) => {
                        return '<iframe width="100%" height="100%" src="https://player.vimeo.com/video/' + data.video_id
                            + '" frameborder="0" allowfullscreen></iframe>'
                    });
                }
            } else {
                let fileType = (data) => {
                    if (data.type.split('/')[0] === 'image') return 'image'
                    if (data.type.split('/')[0] === 'video') return 'video'
                }
                let url = URL.createObjectURL(data)
                if (fileType(data) === 'image') {
                    return `<img src="${url}" style="max-width: 100%; width:100%; height: 100%; max-height: 100%; object-fit: cover;" alt="">`
                }
                if (fileType(data) === 'video') {
                    return `<video height="100%"><source src="${url}" type="${data.type}"></video>`
                }
            }
        },
        removePreviews(index, item) {
            this.previews.splice(index, 1);
            this.previewUrls.splice(index, 1);
        },

        // submit and responses
        submit() {
            this.$toastr.w('Not available.')
        }
    },
    created() {

    }
}
</script>

<style scoped lang="scss">
.preview {
    width: 100%;
    height: 315px;
    object-fit: cover;
    overflow: hidden;

    &--sm {
        width: 32.6%;
        height: 120px;
    }

    &--btn {
        padding: 0 10px;
        color: #fff;
        background: #00000050;
        border-radius: 15px;
        border: none;

        &:hover {
            background: #00000070;
        }
    }
}

.badge-document {
    padding: 3px 3px 3px 15px;
    background-color: #00000010;
}

.post-separator {
    border: 1.5px dashed var(--default-border-color);
    width: 100%;
    margin: 0.5rem 0;
}
</style>