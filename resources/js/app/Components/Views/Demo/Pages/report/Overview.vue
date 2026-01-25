<template>
    <div>
        <app-overlay-loader v-if="preloader" />
        <div v-else>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{$t('new_candidates_this_week')}}</div>
                                <div class="h1">{{ overview.new_candidates }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{$t('move_forward_this_week')}}</div>
                                <div class="h1">{{ overview.moved_forward }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{$t('total_candidates_hired')}} ({{$t('all_time')}})</div>
                                <div class="h1">{{ overview.hired }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('active_jobs') }}</div>
                                <div class="h1">{{ overview.active_jobs }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>{{$t('performance_overview')}}</h4>
                            <app-chart class="mb-primary" type="line-chart" :height="230" :labels="performance.labels"
                                :data-sets="performance.dataSet" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col-12 col-md-6">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>{{$t('top_candidates_source')}}</h4>
                            <app-chart class="mb-primary" type="bar-chart" :height="230" :labels="topCandidates.labels"
                                :data-sets="topCandidates.dataSet" />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>{{$t('new_candidates_by_source')}}</h4>
                            <app-chart class="mb-primary" type="dough-chart" :height="230" :labels="newCandidates.labels"
                                :data-sets="newCandidates.dataSet" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import candidate_flow_response from './json/candidate-flow'
import overview_response from './json/overview.json'
import moment from 'moment';
export default {
    data() {
        return {
            labels: [],
            preloader: true,
            overview: null,
            performance: {
                labels: [],
                dataSet: []
            },
            topCandidates: {
                labels: [],
                dataSet: []
            },
            newCandidates: {
                labels: [],
                dataSet: []
            }
        }
    },
    methods: {
        genChartData(data) {
            return [
                {
                    barPercentage: 0.5,
                    barThickness: 15,
                    borderWidth: 1,
                    borderColor: [
                        "#5a86f1",
                        "#5bc5d5",
                        "#eb779e",
                        "#46cc97",
                        "#368cd5"
                    ],
                    backgroundColor: [
                        "#5a86f1",
                        "#5bc5d5",
                        "#eb779e",
                        "#46cc97",
                        "#368cd5"
                    ],
                    data: data.map(i => i.total_candidates)
                }
            ]
        },
        genPerformanceChartData(data) {
            return [
                {
                    label: 'Total',
                    data: data.map(i =>i.total),
                    backgroundColor: '#5a86f1',
                    borderColor: '#5a86f1',
                    fill: false,
                    cubicInterpolationMode: 'monotone',
                    tension: 0.4
                }, 
                {
                    label: 'Move',
                    data: data.map(i =>i.move_forward),
                    backgroundColor: '#eb779e',
                    borderColor: '#eb779e',
                    fill: false,
                    tension: 0.4
                }, 
                {
                    label: 'Hired',
                    data: data.map(i =>i.hired),
                    backgroundColor: '#46cc97',
                    borderColor: '#46cc97',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        getOverview(query = {}) {
            this.filterForm = query
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    return resolve({ data: overview_response})
                }, 500)
            }).then(res => {
                this.overview = res.data

                let topCandidate = structuredClone(res.data.candidate_sources.sort((a, b) => a.total_candidates - b.total_candidates)).slice(0, 5)
                this.topCandidates.labels = topCandidate.map(i => i.name)
                this.topCandidates.dataSet = this.genChartData(topCandidate)

                this.newCandidates.labels = res.data.candidate_sources.map(i => i.name)
                this.newCandidates.dataSet = this.genChartData(res.data.candidate_sources)
            })
        },
        getCandidateFlow(query = {}) {
            this.filterForm = query
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    return resolve({ data: candidate_flow_response})
                }, 500)
            }).then(res => {
                this.resposne = res.data
                this.performance.labels = res.data.map(i => `${moment(i.start).format('DD MMMM')} - ${moment(i.end).format('DD MMMM')}`)
                this.performance.dataSet = this.genPerformanceChartData(res.data)
            })
        },
    },
    mounted() {
        this.preloader = true;
        Promise.all([this.getOverview(), this.getCandidateFlow()]).finally(() => {
            this.preloader = false
        }).finally(() => this.preloader = false)
    }
}
</script>
