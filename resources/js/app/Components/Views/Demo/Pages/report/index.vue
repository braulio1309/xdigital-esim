<template>
    <app-tab-group :tabs="tabs" />
</template>

<script>
export default {
    data() {
        return {
            tabs: {
                global: {
                    label: 'Reportes de Transacciones',
                    items: [
                        {
                            name: 'Reporte por Plan',
                            title: 'Reporte por Plan',
                            component: "basic-report",
                            props: {},
                            headerHide: true
                        },
                        {
                            name: 'Resumen General',
                            title: 'Resumen General',
                            component: "overview",
                            props: {},
                            headerHide: true
                        },
                        {
                            name: 'Desempeño de Partners',
                            title: 'Desempeño de Partners',
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