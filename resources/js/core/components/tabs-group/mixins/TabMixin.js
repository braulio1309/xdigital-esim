import coreLibrary from "../../../helpers/CoreLibrary";

export const TabMixin = {
    extends: coreLibrary,
    props: {
        tabs: {
            type: Object,
            require: true
        }
    },
    data() {
        return {
            queryStringKey: 'tab',
            queryStringValue: '',
            currentIndex: 0,
            selectedtab: null,
            componentName: '',
            componentId: '',
            componentProps: '',
            componentTitle: '',
            componentButtons: []
        }
    },
    mounted() {
        this.queryStringValue = this.getQueryStringValue(this.queryStringKey);

        this.setTabByName();
    },
    methods: {
        setQueryString(name) {
            const pageTitle = window.document.title;
            window.history.pushState("", pageTitle, `?${this.queryStringKey}=${name}`);
        },

        setTabByName() {
            let currentTab = null

            for (let key in this.tabs) {
                for (let tab of this.tabs[key].items) {
                    if (tab.name == this.queryStringValue) {
                        currentTab = tab;
                    }
                    if (currentTab) break;
                }
            }

            if (!currentTab) {
                for (let key in this.tabs) {
                    let group = this.tabs[key].items
                    if (group.length) {
                        currentTab = group[0]
                    }
                    if (currentTab) break;
                }
            }

            this.loadComponent(currentTab, this.currentIndex);
        },
        loadComponent(tab, index) {
            this.currentIndex = index;
            this.selectedtab = tab;
            this.componentId = tab.component;
            this.componentProps = tab.props;
            if (!this.isUndefined(tab.headerButtons)) {
                this.componentButtons = tab.headerButtons;
            } else this.componentButtons = [];

            this.setQueryString(tab.name);
        }
    }
};
