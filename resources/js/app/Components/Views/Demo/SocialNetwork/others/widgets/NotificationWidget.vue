<template>
    <div class="card rounded-lg" style="overflow: hidden;">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>{{ $t('notifications') }}</div>
<!--                <div>{{ notifications.length }}</div>-->
            </div>
        </div>
        <div class="card-body p-0">
            <div class="d-flex flex-column scroll-y custom-scrollbar" style="max-height: 380px;">
                <div class="py-2 px-3 notification d-flex gap-x-1" v-for="(item, index) in notifications" :key="`notification-${index}-${item.id}`">
                    <img width="24" height="24" class="rounded-circle" :src="item?.profile_picture?.full_url || urlGenerator('/images/avatar-demo.jpg')" alt="">
                    <div class="text-left">
                        <small class="text-muted">{{ item.data.message }}</small>
                    </div>
                </div>
                <div v-if="!notifications.length">
                    <app-empty-data-block message=""/>
                </div>
            </div>
            <button v-if="!no_more" class="btn btn-link w-100" @click="getNotification">{{ $t('more') }}</button>
        </div>
    </div>
</template>

<script>
import {urlGenerator} from "../../../../../../Helpers/AxiosHelper";

export default {
    data() {
        return {
            urlGenerator,
            params: {
                page: 1,
                per_page: 20,
                order_by: 'desc'
            },
            notifications: [],
            no_more: false,
            loading: false
        }
    },
    methods: {
        getNotification(loading = false) {
            this.notifications = [
                {
                    "id": "07551dfa-97fb-41d9-afc1-db8e5ba19d87",
                    "type": "App\\Notifications\\Application\\ReactionNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "Mark Nam reacted to your reply Ut placeat molestiae ....",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 2
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:19.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 2,
                        "first_name": "Mark",
                        "last_name": "Nam",
                        "full_name": "Mark Nam",
                        "profile_picture": null
                    }
                },
                {
                    "id": "e11d4fa3-4f93-4f64-bb42-1bfc4290f0e5",
                    "type": "App\\Notifications\\Application\\ReactionNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "Mark Nam reacted to your comment Occaecati doloremque ....",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 2
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:18.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 2,
                        "first_name": "Mark",
                        "last_name": "Nam",
                        "full_name": "Mark Nam",
                        "profile_picture": null
                    }
                },
                {
                    "id": "d9cdd347-0798-4057-ab0f-57b9b2ad471c",
                    "type": "App\\Notifications\\Application\\ReactionNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "Mark Nam reacted to your reply Sit nobis quod rem e ....",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 2
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:14.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 2,
                        "first_name": "Mark",
                        "last_name": "Nam",
                        "full_name": "Mark Nam",
                        "profile_picture": null
                    }
                },
                {
                    "id": "1a76b807-5b54-43c4-84f9-356092830fb9",
                    "type": "App\\Notifications\\Application\\PostNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "A new post has been created to one of your team by John Doe.",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 1
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:13.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 1,
                        "first_name": "John",
                        "last_name": "Doe",
                        "full_name": "John Doe",
                        "profile_picture": null
                    }
                },
                {
                    "id": "28639851-ae23-4762-8881-2439d19c6a27",
                    "type": "App\\Notifications\\Application\\PostNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "A new post has been created to one of your team by Mark Nam.",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 2
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:13.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 2,
                        "first_name": "Mark",
                        "last_name": "Nam",
                        "full_name": "Mark Nam",
                        "profile_picture": null
                    }
                },
                {
                    "id": "60c2d62a-1f38-44df-8478-b63ff7dbce06",
                    "type": "App\\Notifications\\Application\\PostNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "A new post has been created to one of your team by John Doe.",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 1
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:12.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 1,
                        "first_name": "John",
                        "last_name": "Doe",
                        "full_name": "John Doe",
                        "profile_picture": null
                    }
                },
                {
                    "id": "79be2cd8-096d-48d4-b4da-633bb481e0dc",
                    "type": "App\\Notifications\\Application\\PostNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "A new post has been created to one of your team by Mark Nam.",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 2
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:12.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 2,
                        "first_name": "Mark",
                        "last_name": "Nam",
                        "full_name": "Mark Nam",
                        "profile_picture": null
                    }
                },
                {
                    "id": "a72146fe-0277-4393-82ac-c9b3919679f4",
                    "type": "App\\Notifications\\Application\\PostNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "A new post has been created to one of your team by John Doe.",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 1
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:12.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 1,
                        "first_name": "John",
                        "last_name": "Doe",
                        "full_name": "John Doe",
                        "profile_picture": null
                    }
                },
                {
                    "id": "f4ab46a0-7558-4318-bd60-0df98e729139",
                    "type": "App\\Notifications\\Application\\PostNotification",
                    "notifiable_type": "App\\Models\\Core\\Auth\\User",
                    "notifiable_id": 1,
                    "data": {
                        "message": "A new post has been created to one of your team by Mark Nam.",
                        "name": "John Doe",
                        "url": "http://localhost/admin/dashboard",
                        "notifier_id": 2
                    },
                    "read_at": "2023-03-06T10:07:35.000000Z",
                    "created_at": "2023-02-26T08:01:12.000000Z",
                    "updated_at": "2023-03-06T10:07:35.000000Z",
                    "notifier": {
                        "id": 2,
                        "first_name": "Mark",
                        "last_name": "Nam",
                        "full_name": "Mark Nam",
                        "profile_picture": null
                    }
                }
            ]
        }
    },
    created() {
        this.getNotification(true)
    }
}
</script>

<style scoped lang="scss">
.notification{
    &:hover{
        background-color: #dddddd50;
        color: #000 !important;
    }
}

</style>