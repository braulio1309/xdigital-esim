<template>
    <div class="content-wrapper">
        <div class="row mb-primary">
            <div class="col">
                <div class="d-flex justify-content-between mb-3">
                    <h5>{{ $t('app_pre_loader') }}</h5>
                    <copy-button id="app_pre_loader" :value="AppPreLoader"/>
                </div>
                <div class="card border-0 card-with-shadow position-relative d-flex justify-content-center align-items-center" style="min-height: 100px;">
                    <app-pre-loader />
                </div>
            </div>
        </div>
        <div class="row mb-primary">
            <div class="col">
                <div class="d-flex justify-content-between mb-3">
                    <h5>{{ $t('app_overlay_loader') }}</h5>
                    <copy-button id="app-overlay-loader" :value="AppOverlayLoader"/>
                </div>
                <div class="card border-0 card-with-shadow position-relative" style="min-height: 100px;">
                    <app-overlay-loader/>
                </div>
            </div>
        </div>
        <div class="row mb-primary">
            <div class="col">
                <div>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>{{ $t('app_skeleton_loade') }}</h5>
                        <copy-button id="app-skeleton-loade" :value="AppSkeletonLoader"/>
                    </div>
                    <app-skeleton-loader>
                        <div class="card border-0 card-with-shadow" style="min-height: 100px;">
                            <div class="card-body">
                                <h1 class="d-inlink-block rounded-pill" style="width: 100px;">Test</h1>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex non quam dolorem omnis enim cum consequatur saepe sed asperiores nihil. Architecto provident ratione veniam facilis repudiandae optio blanditiis asperiores magnam!</p>
                            </div>
                        </div>
                    </app-skeleton-loader>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CopyToClipboard from "../../../../../Helpers/CopyToClipboard";
    import CopyButton from "../CopyButton";

    export default {
        components: {CopyButton},
        data() {
            return {
                AppPreLoader: `<app-pre-loader />`,
                AppOverlayLoader: `<app-overlay-loader/>`,
                AppSkeletonLoader: `
                    <app-skeleton-loader>
                        <div class="card border-0 card-with-shadow" style="min-height: 100px;">
                            <div class="card-body">
                                <h1 class="d-inlink-block rounded-pill" style="width: 100px;">Test</h1>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex non quam dolorem omnis enim cum consequatur saepe sed asperiores nihil. Architecto provident ratione veniam facilis repudiandae optio blanditiis asperiores magnam!</p>
                            </div>
                        </div>
                    </app-skeleton-loader>
                `,
            }
        },
        methods: {
            copyIconDom(id, icon) {
                this.iconDomValue = '<app-icon name="' + icon + '"/>';
                setTimeout(() => {
                    CopyToClipboard.copyDom(id);
                    this.$toastr.s(this.$t('icon_copied_successfully'));
                });
            }
        }
    }
</script>
