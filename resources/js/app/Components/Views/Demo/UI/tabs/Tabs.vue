<template>
    <div class="content-wrapper">
        <app-breadcrumb :page-title="$t('tabs')" :directory="$t('ui_elements')" :icon="'trello'"/>

        <div class="mb-primary">
            <div class="d-flex justify-content-between mb-3">
                <h5>{{ $t('tab_horizontal') }}</h5>
                <!--copy button-->
                <copy-button id="tab-horizontal" :value="tabsHorizontalValue"/>
            </div>
            <app-tab :tabs="tabsHorizontal" type="horizontal" />
        </div>
        <div class="mb-primary">
            <div class="d-flex justify-content-between mb-3">
                <h5>{{ $t('tab_vertical') }}</h5>
                <!--copy button-->
                <copy-button id="tab-vertical" :value="tabsVerticalValue"/>
            </div>
            <app-tab :tabs="tabsVertical" :icon="icon" />
        </div>
        <div class="mb-primary">
            <div class="d-flex justify-content-between mb-3">
                <h5>{{ $t('group_tab') }}</h5>
                <!--copy button-->
                <copy-button id="custom-tab" :value="customTabValue"/>
            </div>
            <app-tab-group :tabs="customTabs" />
        </div>
    </div>
</template>

<script>
    import CopyButton from "../CopyButton";

    export default {
        name: "Tabs",
        components: {CopyButton},
        data() {
            return {
                icon: 'settings',
                tabsHorizontal: [
                    {
                        "icon": "send",
                        "name": "delivery",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "lock",
                        "name": "privacy",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "paperclip",
                        "name": "template",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "shield",
                        "name": "custom",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "bell",
                        "name": "notifications",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "bell",
                        "name": "notifications",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "server",
                        "name": "Tab inside tab",
                        "component": "tab-inside-tab",
                        "permission": "",
                        "class": "p-0"
                    },
                ],
                tabsVertical: [
                    {
                        "name": "delivery",
                        "title": "Delivery setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "privacy",
                        "title": "Privacy setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "template",
                        "title": "Template setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "custom",
                        "title": "Custom fields setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "notifications",
                        "title": "Notifications setting",
                        "component": "tab-setting",
                        "permission": ""
                    }
                ],
                customTabs: {
                    group1: {
                        label: 'Group 1',
                        items: [
                            {
                                name: 'delivery',
                                title: 'Delivery setting',
                                component: "tab-setting1",
                                props: {},
                                headerButtons: [
                                    {
                                        label: 'Button 1',
                                        class: "btn btn-primary",
                                        method: (btn) => {}
                                    },
                                    {
                                        label: 'Button 2',
                                        class: "btn btn-outline-primary",
                                        method: (btn) => {}
                                    },
                                ]
                            },
                            {
                                name: 'privacy',
                                title: 'Privacy setting',
                                component: "tab-setting2",
                                props: {},
                                headerHide: true
                            },
                            {
                                name: 'template',
                                title: 'Template setting',
                                component: "tab-setting3",
                                props: {}
                            },
                        ]
                    },
                    group2: {
                        label: 'Group 2',
                        items: [
                            {
                                name: 'custom',
                                title: 'Custom fields setting',
                                component: "tab-setting4",
                                props: {},
                                headerButtons: [
                                    {
                                        label: 'Button 1',
                                        class: "btn btn-primary",
                                        method: (btn) => {}
                                    },
                                    {
                                        label: 'Button 2',
                                        class: "btn btn-outline-primary",
                                        method: (btn) => {}
                                    },
                                ]
                            },
                            {
                                name: 'notifications',
                                title: 'Notifications setting',
                                component: "tab-setting5",
                                props: {}
                            }
                        ]
                    },
                },
                //for copy dom code
                tabsHorizontalValue: `<app-tab :tabs="[
                    {
                        "icon": "send",
                        "name": "delivery",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "lock",
                        "name": "privacy",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "paperclip",
                        "name": "template",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "shield",
                        "name": "custom",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "icon": "bell",
                        "name": "notifications",
                        "component": "tab-setting",
                        "permission": ""
                    }
                ]" type="horizontal"/>`,

                tabsVerticalValue: `<app-tab :tabs="[
                    {
                        "name": "delivery",
                        "title": "Delivery setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "privacy",
                        "title": "Privacy setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "template",
                        "title": "Template setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "custom",
                        "title": "Custom fields setting",
                        "component": "tab-setting",
                        "permission": ""
                    },
                    {
                        "name": "notifications",
                        "title": "Notifications setting",
                        "component": "tab-setting",
                        "permission": ""
                    }
                ]" :icon="icon"/>`,
                customTabValue: `<app-tab-group :tabs="{
                    group1: {
                        label: 'Group 1',
                        items: [
                            {
                                name: 'delivery',
                                title: 'Delivery setting',
                                component: "tab-setting1",
                                props: {},
                                headerButtons: [
                                    {
                                        label: 'Button 1',
                                        class: "btn btn-primary",
                                        method: (btn) => {}
                                    },
                                    {
                                        label: 'Button 2',
                                        class: "btn btn-outline-primary",
                                        method: (btn) => {}
                                    },
                                ]
                            },
                            {
                                name: 'privacy',
                                title: 'Privacy setting',
                                component: "tab-setting2",
                                props: {},
                                headerHide: true
                            },
                            {
                                name: 'template',
                                title: 'Template setting',
                                component: "tab-setting3",
                                props: {}
                            },
                        ]
                    },
                    group2: {
                        label: 'Group 2',
                        items: [
                            {
                                name: 'custom',
                                title: 'Custom fields setting',
                                component: "tab-setting4",
                                props: {},
                                headerButtons: [
                                    {
                                        label: 'Button 1',
                                        class: "btn btn-primary",
                                        method: (btn) => {}
                                    },
                                    {
                                        label: 'Button 2',
                                        class: "btn btn-outline-primary",
                                        method: (btn) => {}
                                    },
                                ]
                            },
                            {
                                name: 'notifications',
                                title: 'Notifications setting',
                                component: "tab-setting5",
                                props: {}
                            }
                        ]
                    },
                }" />`
            }
        }
    }
</script>
