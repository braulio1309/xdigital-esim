<template>
    <div class="vertical-tab d-flex flex-column" style="min-height: 100%;">
        <div class="row no-gutters flex-fill">
            <div class="col-md-3 tab-menu">
                <div class="card border-left-0 border-bottom-0 border-top-0" style="border-color: var(--default-border-color);">
                    <div class="px-primary py-primary">
                        <div class="nav flex-column nav-pills" role="tablist"
                             aria-orientation="vertical">
                            <div v-for="(item, index) in tabs" :key="index">
                                <div class="text-muted text-uppercase font-weight-lighter --font-italic mt-2">{{ item.label }}</div>
                                <a v-for="(tab,index) in item.items" :key="index"
                                    class="text-capitalize tab-item-link d-flex justify-content-between bold my-1 my-sm-2"
                                    :class="{'active': selectedtab?.component === tab.component }"
                                    :id="'v-pills-'+tab.name+'-tab'"
                                    data-toggle="pill"
                                    :href="'#'+tab.name+'-'+index"
                                    @click.prevent="loadComponent(tab)">
                                    <span>{{tab.name}}</span>
                                    <span class="active-icon d-flex align-items-center"><app-icon name="chevron-right"/></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 pt-md-0 pt-sm-4 pt-4">
                <div class="card border-0 h-100">
                    <div class="tab-content px-primary">
                        <div class="tab-pane fade active show" v-if="selectedtab">
                            <div class="d-flex justify-content-between" v-if="!selectedtab.headerHide">
                                <h5 class="d-flex align-items-center text-capitalize mb-0 title tab-content-header">
                                    {{ selectedtab.title }}</h5>
                                <div class="d-flex flex-row-reverse align-items-center mb-0">
                                    <template v-for="(button, index) in componentButtons">
                                        <button class="mr-2" v-if="!isUndefined(button.label)" :key="`components-btn-${index}-${componentId}`" :class="button.class?button.class:'btn btn-primary'"
                                                @click.prevent="headerBtnClicked(button)">
                                            <app-icon :name="button.icon" class="size-20 mr-2" v-if="button.icon"/>
                                            {{button.label}}
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <hr v-if="!selectedtab.headerHide">
                            <div class="content py-primary" style="min-height: 80dvh; height: 100%;">
                                <component :is="selectedtab.component" :props="componentProps" :id="selectedtab.component"></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {TabMixin} from './mixins/TabMixin';

    export default {
        name: "VerticalTab",
        mixins: [TabMixin],
        methods: {
            headerBtnClicked(button) {
                if(!this.isUndefined(button.method)) return button.method(button);
                this.$hub.$emit('headerButtonClicked-' + this.componentId, this.tabs[this.currentIndex])
            }
        }
    }
</script>