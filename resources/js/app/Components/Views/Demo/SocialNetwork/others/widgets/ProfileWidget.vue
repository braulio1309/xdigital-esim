<template>
    <div class="card rounded-lg" style="overflow: hidden;">
        <div class="card-img-top profile__cover bg-info">
            <img v-if="user?.cover_picture" :src="user?.cover_picture?.full_url || urlGenerator('/images/default-cover.png')" alt="cover image"/>
        </div>
        <div class="card-body p-3">
            <div class="d-flex flex-column align-items-center profile__body">
                <img class="profile__picture"
                     :src="user.profile_picture?.full_url || urlGenerator('/images/avatar-demo.jpg')" alt="pp">
                <a :href="urlGenerator('social-profile/')" class="btn btn-link" style="font-size: 16px;">{{ user?.full_name }}</a>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex align-items-center justify-content-center gap-x-2 w-100">
                <div>
                    <app-icon name="rss" :size="16" class="mr-1"/> {{ user.posts_count || 0 }}
                </div>
                <div>
                    <app-icon name="users" :size="16" class="mr-1"/> {{ user.teammates_count || 0 }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {urlGenerator} from "../../../../../../Helpers/AxiosHelper";

export default {
    data() {
        return {
            urlGenerator
        }
    },
    computed: {
        user() {
            return this.$store.state.user.loggedInUser
        }
    },

}
</script>

<style scoped lang="scss">
.profile {
    &__cover {
        height: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    &__picture {
        height: 72px;
        width: 72px;
        border-radius: 50%;
    }

    &__body {
        margin-top: -72px;
    }
}
</style>