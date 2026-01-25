<template>
    <div class="card rounded-lg position-relative" ref="root">
        <div class="card-header bg-transparent p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-x-1">
                    <img width="40" height="40" class="rounded-circle"
                        :src="post.user.profile_picture?.full_url || urlGenerator('/images/avatar-demo.jpg')" alt="">
                    <div class="text-left">
                        <a :href="urlGenerator('social-profile/')" target="_blank">{{
                            post.user.full_name
                        }}</a>
                        <div><small class="text-muted">{{ dateTimeToLocalWithFormat(post.created_at) }}</small></div>
                    </div>
                </div>
                <div>
                    <div class="dropdown options-dropdown d-inline-block">
                        <button type="button" data-toggle="dropdown" title="Actions" class="btn-option btn"
                            aria-expanded="false">
                            <app-icon name="more-vertical" />
                        </button>
                        <div class="dropdown-menu py-2 mt-1" :class="isRtl ? 'dropdown-menu-left' : 'dropdown-menu-right'">
                            <!--                            <a href="#" class="dropdown-item px-4 py-2">Menu 1</a>-->
                            <app-load-more v-if="post.user.id !== user.id" :preloader="preloader" :label="$t('report')"
                                type="submit" class-name="dropdown-item px-4 text-primary py-2" @submit="report" />
                            <button v-if="post.user.id === user.id" @click="edit"
                                class="dropdown-item px-4 text-primary py-2">
                                {{ $t('edit') }}
                            </button>
                            <button v-if="post.user.id === user.id" @click="wannaDel = true"
                                class="dropdown-item px-4 text-danger py-2">{{ $t('delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="text-left" v-html="getTextWithHtml(post.body)"></div>
            <div class="mt-4" v-if="post.images.length">
                <!-- <gallery :galleryID="`gallery-id-${post.id}`" :images="post.images.map(item => item.full_url)"/> -->
            </div>
            <div class="mt-4" v-if="post.documents.length">
                <!-- <documents :documents="post.documents.map(item => item.full_url)"/> -->
            </div>
            <!-- poll -->
            <div class="mt-3" v-if="post.poll">
                <form v-if="!!(post.poll.multiple_answerable)">
                    <div class="d-flex flex-column gap-y-1">
                        <div class="d-flex align-items-center gap-x-2" v-for="(option, index) in post.poll.options"
                            :key="`poll-option-${post.poll.id}-${index}`">
                            <div class="d-flex  form-control" style="padding: 0px !important;">
                                <div class="bg-light p-2 d-flex justify-content-center align-items-center">
                                    <input type="checkbox" :name="post.poll.id" :value="option.body"
                                        style="transform: scale(1.3)"
                                        :checked="option.votes.some(i => i.poll_answer_id === option.id)"
                                        @change="submitPoll(option.id, $event.target.checked)">
                                </div>
                                <div class="w-100 position-relative" style="min-height: 30px;">
                                    <div class="position-absolute" style="top: 0; bottom: 0; left: 0; right: 0;">
                                        <div class="h-100 bg-primary" style="opacity: 10%; transition: .3s ease-in-out;"
                                            :style="{ width: `${votePercentage(option.votes.length)}%` }"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center h-100 px-3">
                                        <label class="m-0" style="z-index: 10">{{ option.body }}</label>
                                        <div style="z-index: 10">{{ votePercentage(option.votes.length) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <form v-else>
                    <div class="d-flex flex-column gap-y-1">
                        <div class="d-flex align-items-center gap-x-2" v-for="(option, index) in post.poll.options"
                            :key="`poll-option-${post.poll.id}-${index}`">
                            <div class="d-flex form-control" style="padding: 0px !important;">
                                <div class="bg-light p-2 d-flex justify-content-center align-items-center">
                                    <input type="radio" :name="post.id" :value="option.body" style="transform: scale(1.3)"
                                        :checked="option.votes.some(i => i.poll_answer_id === option.id)"
                                        @change="submitPoll(option.id, 1)">
                                </div>
                                <div class="w-100 position-relative" style="min-height: 30px;">
                                    <div class="position-absolute" style="top: 0; bottom: 0; left: 0; right: 0;">
                                        <div class="h-100 bg-primary" style="opacity: 10%; transition: .3s ease-in-out;"
                                            :style="{ width: `${votePercentage(option.votes.length)}%` }"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center h-100 px-3">
                                        <label class="m-0" style="z-index: 10">{{ option.body }}</label>
                                        <div style="z-index: 10">{{ votePercentage(option.votes.length) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mt-2">
                    <div v-if="!!(post.poll.is_addable) && !showAddOption">
                        <button type="button" class="btn text-primary px-0 btn-sm" style="box-shadow: none !important;"
                            @click="showAddOption = true">
                            <app-icon name="plus" :size="16" />
                            {{ $t('add_option') }}
                        </button>
                    </div>
                    <div v-if="!!(post.poll.is_addable) && showAddOption" class="d-flex gap-x-1">
                        <div class="flex-fill">
                            <input type="text" class="form-control" :placeholder="$t('enter_to_create')"
                                @keypress.enter="addOption($event.target.value)" />
                        </div>
                        <button type="button" class="btn p-0 btn-clear text-danger" @click="showAddOption = false">
                            <app-icon name="x" :size="16" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer p-0 bg-transparent">

            <!-- reaction and comments count -->
            <div class="px-3 py-2 border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-x-1">
                            <button class="reaction rounded-circle" style="width: 30px; height: 30px;"
                                :class="reacted ? 'text-primary' : ''" @click="reaction">
                                <app-icon :size="16" name="thumbs-up" />
                            </button>
                            <div>{{ reactions_count }}</div>
                        </div>
                    </div>
                    <div>
                        <button @click="commentExpand" class="reaction rounded-pill" style="padding: 3px 15px;">
                            <small>
                                {{ comments_count }}
                                {{ $t('comments') }}
                            </small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Gallery from "../others/Gallery/Gallery";
import Documents from "../others/Documents/Documents";
import NewComment from "./NewComment";
import axios from 'axios'
import extractUrls from "extract-urls"
import { urlGenerator } from "../../../../../Helpers/AxiosHelper";
import { dateTimeToLocalWithFormat } from "../../../../../Helpers/DateTimeHelper";

export default {
    props: ['post', 'recent', 'preloader'],
    components: { Gallery, NewComment, Documents },
    data() {
        return {
            user: window.user,
            urlGenerator,
            dateTimeToLocalWithFormat,
            postGallery: {
                like: ''
            },
            loading: false,
            deleting: false,
            isCommentExpanded: false,
            comments: {
                data: [],
                recentData: []
            },
            comment_options: {
                page: 1,
                per_page: 3
            },
            more_comment: true,
            submitting: false,
            comments_count: 0,
            reactions_count: 0,
            reacted: false,
            wannaDel: false,
            optionForm: {
                body: ''
            },
            showAddOption: false
        }
    },
    methods: {
        submitPoll(value, checked) {
            this.$toastr.w('Not available.')
        },
        addOption(value) {
            axios.post(urlGenerator(POSTS + '/' + this.post.id + '/vote'),
                { option: value }).then(res => {
                    this.post.poll.options = res.data.data.poll.options
                }).finally(() => {
                    this.showAddOption = false
                })
        },
        votePercentage(vote) {
            return Math.round((100 * vote) / this.post.poll.options.reduce((total, cur) => total + cur.votes.length, 0) || 0);
        },
        reaction() {
            this.$toastr.w('Not available.')
        },
        submitComment(event) {
            this.$toastr.w('Not available.')
        },
        commentExpand() {
            this.$toastr.w('Not available.')
        },
        delPost() {
            this.deleting = true
            this.$emit('del', this.post.id)
        },
        edit() {
            this.$emit('edit', this.post.id)
        },
        report() {
            this.$emit('report', { id: this.post.id, type: 'post' })
        },
        recentStyle() {
            let root = this.$refs.root
            root.style.boxShadow = '0 0 8px 1px rgba(172,205,255,0.75)'
            root.style.transition = 'boxShadow 0.5s'
            root.style.border = '1px solid rgb(172,205,255)'
            setTimeout(function () {
                root.style.boxShadow = ''
                root.style.border = ''
            }, 5000)
        },
        getTextWithHtml(text) {
            let urls = extractUrls(text) || []
            for (let url of urls) {
                text = text.replace(url, `<a target="_blank" href="${url}">${url}</a>`);
            }
            return text
        }
    },
    computed: {
        isRtl() {
            return document.querySelector('html').dir === 'rtl'
        }
    },
    mounted() {
        this.comments_count = this.post.comments_count
        this.reactions_count = this.post.reactions_count
        this.reacted = this.post.reacted
        if (this.recent) {
            this.recentStyle()
        }
    },
    watch: {
        recent(n) {
            if (n) {
                this.recentStyle()
            }
        }
    }
}
</script>

<style scoped lang="scss">
.rounded-lg {
    border-radius: 15px !important;
    overflow: hidden;
}

.reaction {
    border: none;
    //border-radius: 5px;
    //padding: 3px 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    background-color: #0c546010;

    &:hover {
        background-color: #0c546030;
    }
}
</style>