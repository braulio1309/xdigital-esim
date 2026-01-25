<template>
    <div class="content-wrapper">
        <div class="container py-4 bg-white">
            <div class="row">
                <div class="col-12 col-md-4" v-if="jobPostSetting?.style[mode]?.jobcard.show">
                    <!-- job card -->
                    <div class="card bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <img class="img-80" :src="icon" alt="icon">
                            </div>
                            <a href="#" class="btn btn-primary text-white btn-block my-3">{{ $t('apply_now') }}</a>
                            <div>
                                <div class="my-2 border-bottom">
                                    <small class="text-muted">{{$t('location')}}</small>
                                    <p>{{ data.is_remote ? $t('remote')+',' : '' }} {{ data.location.address }}</p>
                                </div>
                                <div class="my-2 border-bottom">
                                    <small class="text-muted">{{ $t('salary') }}</small>
                                    <p>{{ data.salary }}{{ data.max_salary ? ` - ${data.max_salary}` : '' }}/{{ $t('year') }}</p>
                                </div>
                                <div class="my-2 border-bottom">
                                    <small class="text-muted">{{ $t('job_type') }}</small>
                                    <p>{{ data.job_type.name }}</p>
                                </div>
                                <div class="my-2 border-bottom">
                                    <small class="text-muted">{{$t('job_posted')}}</small>
                                    <p>{{ moment(data.created_at).format("MMMM Do, YYYY") }}</p>
                                </div>
                                <div class="my-3">
                                    <div class="d-flex gap-x-2">
                                        <a href="#" target="_blank">
                                            <img class="img-20" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjcWcS7oWyHiB4MkHcm7GcCDRl9bXpoZIcr_Da8iuafov6-VHcGVJRz7kKoquFT1T8hYA&usqp=CAU" alt="">
                                        </a>
                                        <a href="#" target="_blank">
                                            <img class="img-20" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjTiK-AtWaobNPgsInlVW4DY03H6Wu4NIybNKRW5KGiRQNWeOAzpp4WtOYWQpzptWIvFw&usqp=CAU" alt="">
                                        </a>
                                        <a href="#" target="_blank">
                                            <img class="img-20" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTn8geLtKmZJmBtu0U4FaKjx4jSzVenTQWTkhoHgA81UbtPalcMpcCcIdGeaVsKXclQ6Xs&usqp=CAU" alt="">
                                        </a>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-outline-secondary btn-block">{{ $t('view_all_jobs') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="preview-content">
                        <div class="preview">
                            <div v-if="jobPostSetting?.style[mode]?.name.show" :style="jobPostSetting?.style[mode]?.name" v-html="data.name" class="mb-3"></div>
                            <div class="text-muted d-flex flex-wrap gap-x-3 mb-5" v-if="jobPostSetting?.style[mode]?.summery.show" >
                                <div class="d-flex gap-x-1 align-items-center"><app-icon name="map-pin" class="size-12" /> {{ data.is_remote ? $t('remote')+',' : '' }} {{ data.location.address }}</div>
                                <div class="d-flex gap-x-1 align-items-center"><app-icon name="dollar-sign" class="size-12" /> {{ data.salary }}{{ data.max_salary ? ` - ${data.max_salary}` : '' }}/{{ $t('year') }}</div>
                                <div class="d-flex gap-x-1 align-items-center"><app-icon name="type" class="size-12" />{{ data.job_type.name }}</div>
                                <div class="d-flex gap-x-1 align-items-center"><app-icon name="calendar" class="size-12" />{{ moment(data.created_at).format("MMMM Do, YYYY") }}</div>
                            </div>

                            <template v-for="(key, index) in Object.keys(data.description)">
                                <template v-if="jobPostSetting?.style[mode]">
                                    <div v-if="jobPostSetting?.style[mode][key].show" class="mb-5" :key="`description-${index}`">
                                        <h5>{{ data?.description[key].title }}</h5>
                                        <div v-html="data.description[key].value"></div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <a href="#" class="btn btn-primary text-white btn-block my-3">{{ $t('apply_now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { urlGenerator } from "../../../../../Helpers/AxiosHelper";
import moment from "moment";
import data from './job_post.json'

export default {
    props: ['applyLink'],
    data() {
        return {
            data,
            moment,
            urlGenerator,
            PUBLIC_CAREER_PAGE: '/',
            year: moment(moment.now()).format("YYYY"),
            viewType: 'desktop',
            activePreview: 'desktop',
            mode: 'dasktop',
            jobPostSetting: typeof data['job_post_settings'] === 'string' ? JSON.parse(data['job_post_settings']) : data['job_post_settings'],
            icon: urlGenerator(window.settings.company_icon),
            logo: urlGenerator(window.settings.company_logo)
        }
    },
    mounted() {
        this.checkViewType();
        window.onresize = () => {
            this.checkViewType();
        }
    },
    methods: {
        checkViewType() {
            this.mode = window.innerWidth > 575 ? 'desktop' : 'mobile';
        }
    }
}
</script>

<style lang="scss">
$spacer: 1rem;


.img{
    &-10{ width: 10px; height: 10px; }
    &-15{ width: 15px; height: 15px; }
    &-20{ width: 20px; height: 20px; }
    &-30{ width: 30px; height: 30px; }
    &-40{ width: 40px; height: 40px; }
    &-50{ width: 50px; height: 50px; }
    &-60{ width: 60px; height: 60px; }
    &-70{ width: 70px; height: 70px; }
    &-80{ width: 80px; height: 80px; }
    &-90{ width: 90px; height: 90px; }
    &-100{ width: 100px; height: 100px; }
    &-x{
        &-10{ width: 10px; }
        &-20{ width: 20px; }
        &-30{ width: 30px; }
        &-40{ width: 40px; }
        &-50{ width: 50px; }
        &-60{ width: 60px; }
        &-70{ width: 70px; }
        &-80{ width: 80px; }
        &-90{ width: 90px; }
        &-100{ width: 100px; }
    }
    &-y {
        &-10{ height: 10px; }
        &-20{ height: 20px; }
        &-30{ height: 30px; }
        &-40{ height: 40px; }
        &-50{ height: 50px; }
        &-60{ height: 60px; }
        &-70{ height: 70px; }
        &-80{ height: 80px; }
        &-90{ height: 90px; }
        &-100{ height: 100px; }
    }
}

.gap {
    &-1 {
        column-gap: $spacer * .25;
        row-gap: $spacer * .25;
    }

    &-2 {
        column-gap: $spacer * .5;
        row-gap: $spacer * .5;
    }

    &-3 {
        column-gap: $spacer;
        row-gap: $spacer;
    }

    &-4 {
        column-gap: $spacer * 1.5;
        row-gap: $spacer * 1.5;
    }

    &-5 {
        column-gap: $spacer * 3;
        row-gap: $spacer * 3;
    }

    &-x {
        &-1 {
            column-gap: $spacer * .25;
        }

        &-2 {
            column-gap: $spacer * .5;
        }

        &-3 {
            column-gap: $spacer;
        }

        &-4 {
            column-gap: $spacer * 1.5;
        }

        &-5 {
            column-gap: $spacer * 3;
        }
    }

    &-y {

        &-1 {
            row-gap: $spacer * .25;
        }

        &-2 {
            row-gap: $spacer * .5;
        }

        &-3 {
            row-gap: $spacer;
        }

        &-4 {
            row-gap: $spacer * 1.5;
        }

        &-5 {
            row-gap: $spacer * 3;
        }
    }
}
</style>