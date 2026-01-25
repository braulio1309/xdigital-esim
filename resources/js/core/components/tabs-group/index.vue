<template>
    <div class="custom-tab-wrapper">
        <vertical-tab :tabs="tabs" />
    </div>
</template>
<script>
import VerticalTab from "./VerticalTab";

export default {
    name: "AppTabCus",
    components: { VerticalTab},
    props: {
        tabs: {
            type: Object,
            require: true
        },
        type: {
            type: String,
            default: 'vertical'
        }
    }
}
</script>

