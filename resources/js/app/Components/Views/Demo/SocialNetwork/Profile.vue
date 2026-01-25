<template>
    <div class="content-wrapper">
        <app-breadcrumb :page-title="$t('profile')"/>
        <div class="row pt-3 p-md-3 justify-content-center">
            <div class="col-12 col-md-8">
                <div class="row">
                    <div class="pt-3 p-md-3">
                        <!-- cover card -->
                        <div class="row">
                            <div class="col p-lg-0">
                                <div class="card rounded-lg position-relative">
                                    <div class="card-img-top profile-cover bg-info cover" style="min-height: 200px;">
                                        <img v-if="profileUser?.coverPicture" :src="profileUser?.coverPicture || urlGenerator('/images/default-cover.png')" class=""
                                             alt="cover image"/>
                                    </div>
                                    <!-- profile picture -->
                                    <div class="avatar d-flex flex-column flex-md-row align-items-center gap-x-1" :class="isRtl ? 'r-20': ''" style="pointer-events: none;">
                                        <div class="rounded-circle profile-uploader position-relative" style="pointer-events: auto;">
                                            <img :src="getImg(profileUser)" alt="pp">
                                        </div>
                                        <div class="text-white text-center text-md-left">
                                            <h4 class="p-0 m-0">{{ profileUser.full_name }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-center justify-content-md-end">
                                            <a href="#" class="btn btn-link py-0 text-primary">
                                                <app-icon class="mr-1" name="user"/>
                                                Profile</a>
                                            <a href="#" class="btn btn-link py-0">
                                                <app-icon class="mr-1" name="image"/>
                                                Gallery</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- content -->
                        <div class="row mt-3">
                            <!-- left panel -->
                            <div class="col-12 col-md-5 col-lg-4 p-lg-0">
                                <div class="card rounded-lg">
                                    <div class="card-body p-4">
                                        <h3>{{ $t('about') }}</h3>
                                        <div>
                                            <p>Excepturi sint eos accusantium hic voluptatem. Itaque praesentium in adipisci dolor cupiditate. Ea dignissimos ut velit.</p>
                                        </div>
                                        <div class="d-flex flex-column gap-y-1">
                                            <div class="d-flex">
                                                <div class="avatar-sm">
                                                    <app-icon :size="20" class="mr-1 mr-md-2" name="shuffle"/>
                                                </div>
                                                <span class="bold">Male</span>
                                            </div>
                                            <div class="d-flex">
                                                <div class="avatar-sm">
                                                    <app-icon :size="20" class="mr-1 mr-md-2" name="map-pin"/>
                                                </div>
                                                <span class="bold">Live at 2554 Connelly Mews Hegmannview, NC 00110-4201</span>
                                            </div>
                                            <div v-if="profileUser.email" class="d-flex">

                                                <div class="avatar-sm">
                                                    <app-icon :size="20" class="mr-1 mr-md-2" name="mail"/>
                                                </div>
                                                <a :href="'mailto:'+profileUser.email">{{ profileUser.email }}</a>
                                            </div>

                                            <div class="d-flex">
                                                <div class="avatar-sm">
                                                    <app-icon :size="20" class="mr-1 mr-md-2" name="phone"/>
                                                </div>
                                                <a class="bold" href="tel:+1 (870) 620-7201">+1 (870) 620-7201</a>
                                            </div>

                                            <div class="d-flex">
                                                <div class="avatar-sm">
                                                    <app-icon :size="20" class="mr-1 mr-md-2" name="gift"/>
                                                </div>
                                                <span class="bold">26-02-1998</span>
                                            </div>

                                            <div v-for="(social, index) in visibleSocialLinks" class="d-flex align-items-center" :key="index">
                                                <div class="avatar-sm">
                                                    <app-icon class="mr-1 mr-md-2" :size="20" :name="social.icon"/>
                                                </div>
                                                <a :href="social.link" target="_blank" class="text-truncate">{{ social.link }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- right panel -->
                            <div class="col-12 col-md-7 col-lg-8 mt-3 mt-md-0" :class="isRtl ? 'pl-lg-0': 'pr-lg-0'">
                                <div class="d-flex flex-column gap-y-1">
                                    <div class="d-flex flex-column gap-y-1">
                                        <app-pre-loader v-if="loading"/>
                                        <template v-else>
                                            <post-view v-for="(post, index) in posts" :post="post" :recent="post.recent" :key="`post-items-${post.id}-${index}`" @edit="edit" @del="del"/>
                                        </template>
                                        <app-empty-data-block v-if="!posts.length && !loading"
                                                              :message="$t('empty_data_block_post')"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
import AppButton from '../../../../../core/components/buttons/AppButton.vue';
import NewPost from "./others/NewPost";
import PostView from "./others/PostView";
import {urlGenerator} from "../../../../Helpers/AxiosHelper";
import {formatDateToLocal} from "../../../../Helpers/Helpers";

export default {
    components: {AppButton, NewPost, PostView},
    data() {
        return {
            location,
            urlGenerator,
            formatDateToLocal,
            posts: [],
            loading: false
        };
    },
    methods: {
        profilePictureChange() {
            this.$toastr.w(this.$t('not_available.'))
        },
        coverChange() {
            this.$toastr.w(this.$t('not_available.'))
        },
        getImg(user) {
            return user.profile_picture?.full_url ? user.profile_picture.full_url : urlGenerator('/images/avatar-demo.jpg');
        },
        del(id) {
            this.$toastr.w(this.$t('not_available.'))
        },
        edit(id) {
            this.$toastr.w(this.$t('not_available.'))
        },
    },
    computed: {
        profileUser() {
            return this.$store.state.user.loggedInUser
        },
        user() {
            return this.$store.state.user.loggedInUser
        },
        visibleSocialLinks() {
            let links = []
            if (this.$store.state.user.loggedInUser.socialLinks) {
                let socialLinks = this.profileUser.socialLinks
                socialLinks.forEach(link => {
                    if (link.pivot?.link) {
                        links.push({
                            icon: link.icon,
                            link: link.pivot.link
                        })
                    }
                })
            }
            return links
        },
        isRtl(){
            return document.querySelector('html').dir === 'rtl'
        }
    },
    mounted() {
        this.posts = [
            {
                "id": 11,
                "uuid": "68aa50cc-1ee5-4b22-bec2-4b2d68d52aca",
                "user_id": 32,
                "body": "Molestias et magni nihil voluptas adipisci. Blanditiis illo ipsam quia vel nostrum sint. Fugiat harum aut vero non voluptas. Sit quasi impedit dolores.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-22T21:35:16.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 1,
                "reactions_count": 2,
                "views_count": 0,
                "votes_count": 4,
                "reacted": false,
                "user": {
                    "id": 32,
                    "first_name": "Addie",
                    "last_name": "Kassulke",
                    "uuid": "32096247-842f-427f-8b74-a44149b7ff8c",
                    "full_name": "Addie Kassulke",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/NeKSpRtqYt6heNIr.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/NeKSpRtqYt6heNIr.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 6,
                    "is_addable": 1,
                    "multiple_answerable": 1,
                    "options": [
                        {
                            "id": 21,
                            "poll_id": 6,
                            "body": "enim",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 21,
                                    "user_id": 16
                                }
                            ]
                        },
                        {
                            "id": 22,
                            "poll_id": 6,
                            "body": "quis",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 22,
                                    "user_id": 32
                                }
                            ]
                        },
                        {
                            "id": 23,
                            "poll_id": 6,
                            "body": "enim",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 24,
                            "poll_id": 6,
                            "body": "dolorem",
                            "votes_count": 2,
                            "votes": [
                                {
                                    "poll_answer_id": 24,
                                    "user_id": 2
                                },
                                {
                                    "poll_answer_id": 24,
                                    "user_id": 14
                                }
                            ]
                        }
                    ]
                }
            },
            {
                "id": 15,
                "uuid": "0d51fcb8-49fc-4437-a134-3fbfd6af162c",
                "user_id": 18,
                "body": "Atque quasi molestiae consectetur consequatur excepturi. Numquam fuga autem molestias adipisci. Incidunt consequuntur et tempora et. Perferendis voluptas dolor iure quia.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-21T16:21:02.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 1,
                "reactions_count": 1,
                "views_count": 5,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 18,
                    "first_name": "Virgie",
                    "last_name": "Kunde",
                    "uuid": "d9deed71-ee91-4241-b977-7f1e2dbc860d",
                    "full_name": "Virgie Kunde",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/8R0DM4LM674EKA9U.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/8R0DM4LM674EKA9U.jpg"
                    },
                    {
                        "path": "/storage/post/YQdMOElZP9Ui8NNx.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/YQdMOElZP9Ui8NNx.jpg"
                    },
                    {
                        "path": "/storage/post/ytUjeF8gIpchIf8N.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/ytUjeF8gIpchIf8N.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 16,
                    "is_addable": 0,
                    "multiple_answerable": 1,
                    "options": [
                        {
                            "id": 61,
                            "poll_id": 16,
                            "body": "vel",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 62,
                            "poll_id": 16,
                            "body": "et",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 63,
                            "poll_id": 16,
                            "body": "reiciendis",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 64,
                            "poll_id": 16,
                            "body": "est",
                            "votes_count": 0,
                            "votes": []
                        }
                    ]
                }
            },
            {
                "id": 16,
                "uuid": "c26debec-3281-4698-8ad3-b8568a97a195",
                "user_id": 31,
                "body": "Sit qui et aliquid incidunt molestiae veritatis. Qui doloribus et autem omnis odio iusto et ut. Assumenda voluptas veniam est facilis eveniet ut modi. Nostrum maiores consequuntur et qui. Dignissimos in omnis sunt et expedita labore ex.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-21T00:24:40.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 2,
                "reactions_count": 2,
                "views_count": 2,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 31,
                    "first_name": "Dolores",
                    "last_name": "Schimmel",
                    "uuid": "b019b9d1-02f1-4284-af15-97c13face2f5",
                    "full_name": "Dolores Schimmel",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/kXK7r2Kbns8BJxc0.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/kXK7r2Kbns8BJxc0.jpg"
                    },
                    {
                        "path": "/storage/post/3rrFdGXdGCJKJjX3.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/3rrFdGXdGCJKJjX3.jpg"
                    },
                    {
                        "path": "/storage/post/BJnrRYXC72UTf2kz.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/BJnrRYXC72UTf2kz.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 13,
                "uuid": "b520d743-3ca6-4356-80f4-0ca96db95b99",
                "user_id": 21,
                "body": "Omnis saepe illum vitae. Accusamus quis eum mollitia autem recusandae voluptatem. Accusamus dolorem ipsum reiciendis corporis et.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-19T06:30:25.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 1,
                "reactions_count": 1,
                "views_count": 4,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 21,
                    "first_name": "Deborah",
                    "last_name": "Nienow",
                    "uuid": "6effac8f-9187-4172-8645-010f70180a00",
                    "full_name": "Deborah Nienow",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/GsfARaU4dzaH6ErJ.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/GsfARaU4dzaH6ErJ.jpg"
                    },
                    {
                        "path": "/storage/post/XX2ndrfvUMIF0yLG.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/XX2ndrfvUMIF0yLG.jpg"
                    },
                    {
                        "path": "/storage/post/jLbFKMBuqb2ZheB3.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/jLbFKMBuqb2ZheB3.jpg"
                    },
                    {
                        "path": "/storage/post/IaGYVwWklr0iZ1XB.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/IaGYVwWklr0iZ1XB.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 22,
                "uuid": "e575fe14-5f48-4056-b8ec-7482d972928b",
                "user_id": 13,
                "body": "Ullam quia aperiam sed. Quae eum in soluta illo laborum expedita laudantium nemo. Aut qui libero eius libero. Eum consequatur in non autem.",
                "status_id": 8,
                "shared_with": "public",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-18T12:22:21.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 0,
                "reactions_count": 5,
                "views_count": 3,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 13,
                    "first_name": "Dawn",
                    "last_name": "Daniel",
                    "uuid": "27963244-4323-4ec5-858a-f9c54c6b3d50",
                    "full_name": "Dawn Daniel",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/VfG5FkziyYyK7i2E.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/VfG5FkziyYyK7i2E.jpg"
                    },
                    {
                        "path": "/storage/post/dXib8QdutCnz4TaH.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/dXib8QdutCnz4TaH.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 29,
                "uuid": "64ae76eb-741f-40d2-bc84-b01d5ec9494e",
                "user_id": 6,
                "body": "Nisi tempora ut rerum eos consectetur. Eveniet iste quam commodi voluptatum ut. Explicabo nesciunt nostrum est iste rerum.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-18T02:11:16.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 0,
                "reactions_count": 3,
                "views_count": 1,
                "votes_count": 1,
                "reacted": false,
                "user": {
                    "id": 6,
                    "first_name": "Pat",
                    "last_name": "Mann",
                    "uuid": "136be096-05a8-43f9-a870-9b2751ff0a37",
                    "full_name": "Pat Mann",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/nZunQPK88BN1hj9a.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/nZunQPK88BN1hj9a.jpg"
                    },
                    {
                        "path": "/storage/post/8o0wqh3SAgeSDPZd.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/8o0wqh3SAgeSDPZd.jpg"
                    },
                    {
                        "path": "/storage/post/OvTgUzN70BucLSxM.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/OvTgUzN70BucLSxM.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 5,
                    "is_addable": 0,
                    "multiple_answerable": 0,
                    "options": [
                        {
                            "id": 17,
                            "poll_id": 5,
                            "body": "veritatis",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 18,
                            "poll_id": 5,
                            "body": "rerum",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 19,
                            "poll_id": 5,
                            "body": "optio",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 20,
                            "poll_id": 5,
                            "body": "dolor",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 20,
                                    "user_id": 17
                                }
                            ]
                        }
                    ]
                }
            },
            {
                "id": 27,
                "uuid": "96f71a1f-b4dd-424e-852a-43185dbddb57",
                "user_id": 13,
                "body": "Dolorum at quibusdam voluptatum beatae molestiae. Necessitatibus asperiores facere rerum et unde omnis voluptatum atque. Excepturi quia laboriosam sit unde qui. Adipisci quae et voluptatem animi ratione est a sit.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-16T23:59:20.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 3,
                "reactions_count": 0,
                "views_count": 4,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 13,
                    "first_name": "Dawn",
                    "last_name": "Daniel",
                    "uuid": "27963244-4323-4ec5-858a-f9c54c6b3d50",
                    "full_name": "Dawn Daniel",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/c50twR5Q4bm6zumS.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/c50twR5Q4bm6zumS.jpg"
                    },
                    {
                        "path": "/storage/post/esuY8pWSlJ0IEMVo.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/esuY8pWSlJ0IEMVo.jpg"
                    },
                    {
                        "path": "/storage/post/vLXmYV6d7BT7W86g.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/vLXmYV6d7BT7W86g.jpg"
                    },
                    {
                        "path": "/storage/post/DjqUz0hcl9HynT8C.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/DjqUz0hcl9HynT8C.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            },
            {
                "id": 10,
                "uuid": "8987faf1-f846-469b-a8bc-145d483d39e7",
                "user_id": 2,
                "body": "Neque perferendis consequatur non facilis. Et quaerat ut dolores sed maiores exercitationem aliquid reiciendis. Ratione ipsam sed accusamus ex.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-16T23:15:23.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 3,
                "reactions_count": 2,
                "views_count": 2,
                "votes_count": 4,
                "reacted": false,
                "user": {
                    "id": 2,
                    "first_name": "Mark",
                    "last_name": "Nam",
                    "uuid": "41b6bcbd-d1b1-4550-9f37-f09d0e193e8b",
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
                        "path": "/storage/post/PsVtLw0OXWu4RVFJ.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/PsVtLw0OXWu4RVFJ.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 7,
                    "is_addable": 0,
                    "multiple_answerable": 1,
                    "options": [
                        {
                            "id": 25,
                            "poll_id": 7,
                            "body": "tenetur",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 25,
                                    "user_id": 2
                                }
                            ]
                        },
                        {
                            "id": 26,
                            "poll_id": 7,
                            "body": "sed",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 26,
                                    "user_id": 20
                                }
                            ]
                        },
                        {
                            "id": 27,
                            "poll_id": 7,
                            "body": "sit",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 27,
                                    "user_id": 27
                                }
                            ]
                        },
                        {
                            "id": 28,
                            "poll_id": 7,
                            "body": "consequatur",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 28,
                                    "user_id": 10
                                }
                            ]
                        }
                    ]
                }
            },
            {
                "id": 18,
                "uuid": "0eb387f7-c34a-48a8-92e3-8537bc091b06",
                "user_id": 11,
                "body": "Ducimus quia rerum cupiditate. Enim aut dolor explicabo odit. Unde qui provident unde debitis. Error molestiae asperiores sequi deleniti ut molestiae voluptatum.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-16T10:54:36.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 0,
                "reactions_count": 2,
                "views_count": 5,
                "votes_count": 5,
                "reacted": false,
                "user": {
                    "id": 11,
                    "first_name": "Judy",
                    "last_name": "Rohan",
                    "uuid": "362b7b0c-e061-4225-99ff-09cb420a10f3",
                    "full_name": "Judy Rohan",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/fc2KTIhHDD923Qug.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/fc2KTIhHDD923Qug.jpg"
                    },
                    {
                        "path": "/storage/post/ke8qPDZ340zlzLRn.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/ke8qPDZ340zlzLRn.jpg"
                    }
                ],
                "documents": [],
                "poll": {
                    "id": 3,
                    "is_addable": 0,
                    "multiple_answerable": 0,
                    "options": [
                        {
                            "id": 9,
                            "poll_id": 3,
                            "body": "autem",
                            "votes_count": 1,
                            "votes": [
                                {
                                    "poll_answer_id": 9,
                                    "user_id": 26
                                }
                            ]
                        },
                        {
                            "id": 10,
                            "poll_id": 3,
                            "body": "doloribus",
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
                            "body": "aperiam",
                            "votes_count": 0,
                            "votes": []
                        },
                        {
                            "id": 12,
                            "poll_id": 3,
                            "body": "soluta",
                            "votes_count": 3,
                            "votes": [
                                {
                                    "poll_answer_id": 12,
                                    "user_id": 3
                                },
                                {
                                    "poll_answer_id": 12,
                                    "user_id": 8
                                },
                                {
                                    "poll_answer_id": 12,
                                    "user_id": 28
                                }
                            ]
                        }
                    ]
                }
            },
            {
                "id": 20,
                "uuid": "5413c191-ea1a-4d25-9cfd-eaad78945662",
                "user_id": 21,
                "body": "Ipsam eius voluptate voluptatum enim tenetur rerum. Doloremque nam sint aut sint assumenda. Molestiae dolores magnam autem quaerat voluptates pariatur. Suscipit ut quis cumque sunt dolor.",
                "status_id": 8,
                "shared_with": "team",
                "shared_post_id": null,
                "deleted_at": null,
                "created_at": "2023-03-14T11:31:19.000000Z",
                "updated_at": "2023-03-23T06:03:56.000000Z",
                "comments_count": 1,
                "reactions_count": 1,
                "views_count": 5,
                "votes_count": 0,
                "reacted": false,
                "user": {
                    "id": 21,
                    "first_name": "Deborah",
                    "last_name": "Nienow",
                    "uuid": "6effac8f-9187-4172-8645-010f70180a00",
                    "full_name": "Deborah Nienow",
                    "profile_picture": null
                },
                "status": {
                    "id": 8,
                    "name": "status_published",
                    "translated_name": "default.status_published"
                },
                "images": [
                    {
                        "path": "/storage/post/oRYtTv7yaKduFS2T.jpg",
                        "full_url": "https://inlink.gainhq.com/storage/post/oRYtTv7yaKduFS2T.jpg"
                    }
                ],
                "documents": [],
                "poll": null
            }
        ]
    }
};
</script>
<style scoped lang="scss">

.avatar {
    position: absolute;
    bottom: 52px;
    width: 100%;

    img {
        width: 72px;
        height: 72px;
    }
}

.rounded-lg {
    border-radius: 15px !important;
    overflow: hidden;
}

.profile-cover {
    object-fit: cover;
    img{
        min-height: 148px;
        max-height: 148px;
    }
}

.bold {
    font-weight: bold !important;
}

.profile-uploader {
    cursor: pointer;
    overflow: hidden;

    &--text {
        transition: .2s ease-in;
        display: flex;
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #00000080;
        z-index: 900;
        color: #fff;
    }

    &:hover {
        .profile-uploader--text {
            opacity: 1;
        }
    }
}

.cover {
    overflow: hidden;
    position: relative;

    img {
        width: 100%;
        object-fit: cover;
    }

    &-change-button {
        opacity: 0;
        top: 10px;
        left: 10px;
    }

    &:hover .cover-change-button {
        opacity: 1;
    }
}

.cover-change-button {
}

@media screen and (min-width: 768px) {
    .profile-cover {
        max-height: 200px;
        height: auto;
    }

    .avatar {
        position: absolute;
        bottom: 20px;
        left: 20px;
        width: auto;

        img {
            width: 112px;
            height: 112px;
        }
    }
    .r-20{
        left: auto;
        right: 20px;
    }
}
</style>