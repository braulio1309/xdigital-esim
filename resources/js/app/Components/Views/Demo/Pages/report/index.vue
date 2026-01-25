<template>
    <app-tab-group :tabs="tabs" />
</template>

<script>
export default {
    data() {
        return {
            tabs: {
                global: {
                    label: 'Report',
                    items: [
                        {
                            name: this.$t('basic_report'),
                            title: this.$t('basic_report'),
                            component: "basic-report",
                            props: {},
                            headerHide: true
                        },
                        {
                            name: this.$t('overview'),
                            title: this.$t('overview'),
                            component: "overview",
                            props: {},
                            headerHide: true
                        },
                        {
                            name: this.$t('job_overview'),
                            title: this.$t('job_overview'),
                            component: "job-overview",
                            props: {},
                            headerHide: true
                        },
                    ]
                }
            }
        }
    }
}
</script>