<template>
    <div class="content-wrapper">
        <app-breadcrumb :page-title="$t('feeds')" />
        <div class="row pt-3 p-md-3 justify-content-center">

            <div class="col-12 col-md-8">
                <div class="row">
                    <!-- center panel -->
                    <div class="col-12 col-lg-9">
                        <div class="d-flex flex-column gap-y-1">
                            <new-post v-if="!readonly" nofetch />
                            <div class="d-flex flex-column gap-y-1">
                                <!-- post list view -->
                                <template>
                                    <post-view v-for="(post, index) in posts" :post="post" :preloader="preloader"
                                        :recent="post.recent" :key="`post-items-${post.id}`" @edit="edit" @del="del"
                                        @report="reportModalOpen" />
                                </template>
                                <app-pre-loader v-if="loading" />
                                <app-empty-data-block v-if="!posts.length && !loading"
                                    :message="$t('empty_data_block_post')" />
                            </div>
                        </div>
                    </div>
                    <!-- right panel -->
                    <div class="col-12 d-none d-lg-flex flex-column gap-y-1 col-lg-3 p-lg-0">
                        <!-- profile widget -->
                        <profile-widget />

                        <!-- teammates widget -->
                        <teammate-widget />

                        <!-- notification widget -->
                        <notification-widget />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Components
import NewPost from './others/NewPost'
import PostView from './others/PostView'
import ListItem from "./others/ListItem";
// Widgets
import ProfileWidget from "./others/widgets/ProfileWidget";
import TeammateWidget from "./others/widgets/TeammateWidget";
import NotificationWidget from "./others/widgets/NotificationWidget";
import { urlGenerator } from "../../../../Helpers/AxiosHelper";

export default {
    components: { NewPost, PostView, ListItem, ProfileWidget, TeammateWidget, NotificationWidget },
    props: {
        readonly: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            reportData: null,
            selectedUrl: '',
            isReportModalActive: false,
            preloader: false,
            urlGenerator,
            leftPanel: [
                { id: 1, title: 'news feed', icon: 'rss', url: urlGenerator('/feed'), target: '' },
                { id: 2, title: 'admin', icon: 'settings', url: urlGenerator('/admin/dashboard'), target: '_blank' },
            ],
            wannaEdit: false,
            selectedPostId: null,
            loading: false,
            posts: [],
            teams: [],
            teamsLoading: false
        }
    },
    methods: {
        isActive(url) {
            return window.location.href === url ? 'text-primary' : ''
        },
        del(id) {
            this.$toastr.w('This Feature not available now')
        },
        reportModalOpen(data) {
            this.$toastr.w('This Feature not available now')
        },
        edit(id) {
            this.$toastr.w('This Feature not available now')
        }
    },
    created() {
        this.posts = [
            {
                "id": 2,
                "uuid": "eee684ac-a7ff-48ea-810d-59eb1adec0fc",
                "user_id": 1,
                "body": "Dolor accusantium et quia esse qui quibusdam qui id. Autem sed voluptatibus pariatur nihil. Voluptatem eos alias et eaque veniam. Ab ea unde id aperiam.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-22T06:03:19.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 0,
                "reactions_count": 0,
                "views_count": 0,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 1,
                    "first_name": "John",
                    "last_name": "Doe",
                    "uuid": "7b03d41a-6e61-4984-aeb4-e51f84dde2ef",
                    "full_name": "John Doe",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/Ok1gIBwXqq9aZWtW.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/Ok1gIBwXqq9aZWtW.jpg"
                    },
                    {
                        "path": "/storage/post/S9GLLe6xCnInQSLc.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/S9GLLe6xCnInQSLc.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 20,
                    "is_addable": 1,
                    "multiple_answerable": 0,
                    "options": [
                        {
                            "id": 77,
                            "poll_id": 20,
                            "body": "totam",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 78,
                            "poll_id": 20,
                            "body": "et",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 79,
                            "poll_id": 20,
                            "body": "ut",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 80,
                            "poll_id": 20,
                            "body": "aut",
                            "votes_count": 0,
                            "votes": []
                        }
                    ]
                }
            },
            {
                "id": 1,
                "uuid": "ace2d034-0ec3-491c-b146-03e330601192",
                "user_id": 1,
                "body": "Nihil est qui adipisci mollitia. Suscipit velit autem tempore doloremque et necessitatibus fugiat quia. Vero alias a velit similique minus. Laborum cumque cupiditate sit quo deserunt odio et facilis.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-19T23:38:21.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 0,
                "reactions_count": 0,
                "views_count": 0,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 1,
                    "first_name": "John",
                    "last_name": "Doe",
                    "uuid": "7b03d41a-6e61-4984-aeb4-e51f84dde2ef",
                    "full_name": "John Doe",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/wp9WFLHPlMrKGwie.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/wp9WFLHPlMrKGwie.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 17,
                    "is_addable": 1,
                    "multiple_answerable": 0,
                    "options": [
                        {
                            "id": 65,
                            "poll_id": 17,
                            "body": "hic",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 66,
                            "poll_id": 17,
                            "body": "magnam",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 67,
                            "poll_id": 17,
                            "body": "ut",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 68,
                            "poll_id": 17,
                            "body": "quo",
                            "votes_count": 0,
                            "votes": []
                        }
                    ]
                }
            },
            {
                "id": 11,
                "uuid": "83cda577-cce8-4114-8ea2-be3275c48155",
                "user_id": 22,
                "body": "Et omnis dolor placeat adipisci laborum. Eos ab quo maxime eos. Rerum sit et iusto cumque et cumque.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-19T22:50:55.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 2,
                "reactions_count": 3,
                "views_count": 0,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 22,
                    "first_name": "Caden",
                    "last_name": "Schowalter",
                    "uuid": "6820252b-2ca2-4f6b-8cad-c04e6c3875c8",
                    "full_name": "Caden Schowalter",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/CIrHaFINyqD5Z9SX.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/CIrHaFINyqD5Z9SX.jpg"
                    },
                    {
                        "path": "/storage/post/HHgeheKlL2SW34Al.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/HHgeheKlL2SW34Al.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 26,
                "uuid": "14be0ba4-887d-4352-8d2f-4f44ce929386",
                "user_id": 31,
                "body": "Non et architecto adipisci consectetur modi quos. Necessitatibus officia magnam et laboriosam. Qui nihil voluptatibus iusto et est nostrum tempora.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-19T22:50:01.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 4,
                "reactions_count": 4,
                "views_count": 5,
                "votes_count": 3,
                "reacted": false,
                "user": {
                    "id": 31,
                    "first_name": "Riley",
                    "last_name": "Zemlak",
                    "uuid": "a64672fa-41bd-42a0-9fe1-78303b60cdf6",
                    "full_name": "Riley Zemlak",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/cyPGg6Nf0WwKJ4jq.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/cyPGg6Nf0WwKJ4jq.jpg"
                    },
                    {
                        "path": "/storage/post/q5GHyuMHpw1qR5fY.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/q5GHyuMHpw1qR5fY.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 19,
                    "is_addable": 0,
                    "multiple_answerable": 0,
                    "options": [
                        {
                            "id": 73,
                            "poll_id": 19,
                            "body": "at",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 73,
                                    "user_id": 18
                                }
                            ]
                        },
                        {
                            "id": 74,
                            "poll_id": 19,
                            "body": "quis",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 75,
                            "poll_id": 19,
                            "body": "nobis",
                            "votes_count": 2,
                            "votes": [
                                {
                                    "poll_answer_id": 75,
                                    "user_id": 13
                                },
                                {
                                    "poll_answer_id": 75,
                                    "user_id": 14
                                }
                            ]
                        },
                        {
                            "id": 76,
                            "poll_id": 19,
                            "body": "sed",
                            "votes_count": 0,
                            "votes": []
                        }
                    ]
                }
            },
            {
                "id": 28,
                "uuid": "4b69356d-aa73-4184-8888-049534540ca4",
                "user_id": 14,
                "body": "Ullam dolor quia voluptatum omnis corporis officiis tenetur magnam. Fugiat voluptas omnis et temporibus expedita libero. Dolorem dolorem eveniet sit consequatur aspernatur laborum.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-19T19:32:00.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 4,
                "reactions_count": 1,
                "views_count": 1,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 14,
                    "first_name": "Helga",
                    "last_name": "Ebert",
                    "uuid": "bf9e7cd6-14f8-4825-b893-d284b9499870",
                    "full_name": "Helga Ebert",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/mfBYcBbvS4hKDG25.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/mfBYcBbvS4hKDG25.jpg"
                    },
                    {
                        "path": "/storage/post/yiqw7KC6ESPGlJtM.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/yiqw7KC6ESPGlJtM.jpg"
                    },
                    {
                        "path": "/storage/post/kDFU69xXmMPMeDgc.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/kDFU69xXmMPMeDgc.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 17,
                "uuid": "361b42d1-e0f7-48dd-bad9-d8e2c4ff2f52",
                "user_id": 1,
                "body": "Eaque et non aliquid sed deserunt. Magnam eos sapiente ipsum itaque sed. Quibusdam odit possimus a esse est. Provident ea odit aut delectus qui ut magni. Eos quod quis sapiente iure.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-19T10:45:37.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 5,
                "reactions_count": 1,
                "views_count": 5,
                "votes_count": 1,
                "reacted": false,
                "user": {
                    "id": 1,
                    "first_name": "John",
                    "last_name": "Doe",
                    "uuid": "7b03d41a-6e61-4984-aeb4-e51f84dde2ef",
                    "full_name": "John Doe",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/OtCPz6jCJ5RgA97W.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/OtCPz6jCJ5RgA97W.jpg"
                    },
                    {
                        "path": "/storage/post/e6k4qXkfLyxeeMDq.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/e6k4qXkfLyxeeMDq.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 14,
                    "is_addable": 0,
                    "multiple_answerable": 1,
                    "options": [
                        {
                            "id": 53,
                            "poll_id": 14,
                            "body": "omnis",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 53,
                                    "user_id": 28
                                }
                            ]
                        },
                        {
                            "id": 54,
                            "poll_id": 14,
                            "body": "impedit",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 55,
                            "poll_id": 14,
                            "body": "rerum",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 56,
                            "poll_id": 14,
                            "body": "sed",
                            "votes_count": 0,
                            "votes": []
                        }
                    ]
                }
            },
            {
                "id": 23,
                "uuid": "61145363-c370-402f-aed6-961f1f11bfc4",
                "user_id": 20,
                "body": "Ut quibusdam qui earum et. Laboriosam sed qui eaque. Non odio quos rerum esse sit sed quia. Dolores magni et aspernatur molestiae animi non sed in.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-18T09:15:50.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 3,
                "reactions_count": 3,
                "views_count": 1,
                "votes_count": 1,
                "reacted": false,
                "user": {
                    "id": 20,
                    "first_name": "Myriam",
                    "last_name": "Farrell",
                    "uuid": "2fdab7e0-dd22-49c7-b1b3-b06eca143964",
                    "full_name": "Myriam Farrell",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/puo7upTPped4mbQ1.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/puo7upTPped4mbQ1.jpg"
                    },
                    {
                        "path": "/storage/post/CfJ6pcOcp8wi1zAf.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/CfJ6pcOcp8wi1zAf.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 12,
                    "is_addable": 0,
                    "multiple_answerable": 0,
                    "options": [
                        {
                            "id": 45,
                            "poll_id": 12,
                            "body": "molestias",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 45,
                                    "user_id": 13
                                }
                            ]
                        },
                        {
                            "id": 46,
                            "poll_id": 12,
                            "body": "excepturi",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 47,
                            "poll_id": 12,
                            "body": "error",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 48,
                            "poll_id": 12,
                            "body": "sint",
                            "votes_count": 0,
                            "votes": []
                        }
                    ]
                }
            },
            {
                "id": 13,
                "uuid": "af5ae731-4b7a-4413-949f-e56e47c12f68",
                "user_id": 9,
                "body": "Temporibus ab sed praesentium aliquam eaque nostrum. Modi ipsam eos iure omnis aut. Accusantium qui quis rerum non repellat aut animi. Sequi neque hic qui sint.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-18T06:07:02.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 4,
                "reactions_count": 1,
                "views_count": 2,
                "votes_count": 2,
                "reacted": false,
                "user": {
                    "id": 9,
                    "first_name": "Alfonzo",
                    "last_name": "Runolfsson",
                    "uuid": "4ab9d025-452f-453a-9b73-413a0e3574fc",
                    "full_name": "Alfonzo Runolfsson",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/NxpgAvyKsgP4DQ4C.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/NxpgAvyKsgP4DQ4C.jpg"
                    },
                    {
                        "path": "/storage/post/EA1N63W5A20a9zbO.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/EA1N63W5A20a9zbO.jpg"
                    },
                    {
                        "path": "/storage/post/wFqCE6dyAi9ETgeH.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/wFqCE6dyAi9ETgeH.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 3,
                    "is_addable": 1,
                    "multiple_answerable": 1,
                    "options": [
                        {
                            "id": 9,
                            "poll_id": 3,
                            "body": "sapiente",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 10,
                            "poll_id": 3,
                            "body": "necessitatibus",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 10,
                                    "user_id": 11
                                }
                            ]
                        },
                        {
                            "id": 11,
                            "poll_id": 3,
                            "body": "fuga",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 12,
                            "poll_id": 3,
                            "body": "rerum",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 12,
                                    "user_id": 19
                                }
                            ]
                        }
                    ]
                }
            },
            {
                "id": 7,
                "uuid": "d03ac21d-526d-4e25-8ff7-10db90b5a751",
                "user_id": 2,
                "body": "Aut ratione eligendi quae sint nulla officiis similique quo. Enim autem ratione delectus sequi ut voluptate dolor. Molestias velit dignissimos nam. Ut et enim sed nihil sed.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-17T21:53:59.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 4,
                "reactions_count": 5,
                "views_count": 5,
                "votes_count": 0,
                "reacted": true,
                "user": {
                    "id": 2,
                    "first_name": "Mark",
                    "last_name": "Nam",
                    "uuid": "308b3a91-b17f-4a55-b2e8-bc33a73507d5",
                    "full_name": "Mark Nam",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/1cCJOpFhhGj5WXRe.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/1cCJOpFhhGj5WXRe.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 19,
                "uuid": "32747514-3efe-416b-8d3e-16d3872b8219",
                "user_id": 18,
                "body": "Iste expedita ut tempora illum velit dolores dolor. Eum non tempora ut voluptas explicabo sint perferendis. Deserunt vero et aut non adipisci. Molestiae non porro in fugit et.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-17T12:22:50.000000Z",
                "updated_at": "2023-03-22T12:04:32.000000Z",
                "comments_count": 3,
                "reactions_count": 1,
                "views_count": 3,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 18,
                    "first_name": "Ima",
                    "last_name": "Lubowitz",
                    "uuid": "cd2b9bdc-2a80-4cc5-8df8-0fa554b65223",
                    "full_name": "Ima Lubowitz",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/coK9zXAXH78Z7qOV.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/coK9zXAXH78Z7qOV.jpg"
                    },
                    {
                        "path": "/storage/post/uFrP2Jbs82D3riHB.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/uFrP2Jbs82D3riHB.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            }
        ]
        this.teams = []
    }
}
</script>

<style scoped>
.rounded-lg {
    border-radius: 15px !important;
    /*overflow: hidden;*/
}
</style>