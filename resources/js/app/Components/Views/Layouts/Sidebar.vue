<template>
    <sidebar :data="data"
             :logo-url="urlGenerator('/')"
             :logo-src="urlGenerator(logo || AppFunction.getAppUrl('images/logo.png'))"
             :logo-icon-src="urlGenerator(logoIcon || AppFunction.getAppUrl('images/icon.png'))"
    />
</template>

<script>
import {urlGenerator} from "../../../Helpers/AxiosHelper";
import AppFunction from "../../../../core/helpers/app/AppFunction";

export default {
    name: 'DashboardSidebar',
    props: {
        data: {
            required: true
        },
        logo: {
            type: String,
        },
        logoIcon: {
            type: String,
        }
    },
    data() {
        return {
            urlGenerator,
        }
    }
}
</script>
