<template>
    <div
        class="container-fluid message-container p-0 d-flex flex-column align-items-center justify-content-center text-center">
        <div class="text-success mb-5">
            <app-icon class="size-100" name="mail"/>
        </div>
        <h1 class="text-muted">
            {{ $t('user_verified_successfully') }}
        </h1>
    </div>
</template>
<script>

import {urlGenerator} from "../../../Helpers/AxiosHelper";

export default {
    name: "UserVerified",
    data() {
        return {
            urlGenerator,
        };
    },
    mounted() {
        setTimeout(()=>{
            window.location = urlGenerator('/');
        }, 2000)
    },
};
</script>
<style scoped>
.message-container {
    height: 100vh;
}
</style>