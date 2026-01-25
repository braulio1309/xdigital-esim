<template>
    <div>
        <app-overlay-loader v-if="preloader" />
        <div v-else>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('total_jobs') }}({{ $t('all_time') }})</div>
                                <div class="h1">{{ total_job }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('live_job') }}</div>
                                <div class="h1">{{ live_job }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('avg_candidates_per_job') }}</div>
                                <div class="h1">{{ avg_candidate_per_job }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>New Candidates By Job</h4>
                            <app-chart class="mb-primary" type="horizontal-line-chart" :height="230"
                                :labels="newCandidates.labels" :data-sets="newCandidates.dataSet" />

                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>{{ $t('active_job_by_month') }}</h4>
                            <app-chart class="mb-primary" type="bar-chart" :height="230" :labels="activeJobByMonth.labels"
                                :data-sets="activeJobByMonth.dataSet" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import job_overview_response from './json/job-overview.json'
import { colorArray } from '../../../../../../app/Helpers/ColorHelper';
export default {
    data() {
        return {
            preloader: false,
            total_job: 0,
            live_job: 0,
            avg_candidate_per_job: 0,
            newCandidates: {
                labels: [],
                dataSet: []
            },
            activeJobByMonth: {
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
                    borderColor: colorArray.slice(0, data.length),
                    backgroundColor: colorArray.slice(0, data.length),
                    data: data.map(i => i.value)
                }
            ]
        },
        getJobOverview(query = {}) {
            this.filterForm = query
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    return resolve({ data: job_overview_response })
                }, 500)
            }).then(res => {
                this.overview = res.data
                this.total_job = res.data.total_job
                this.live_job = res.data.live_job
                this.avg_candidate_per_job = res.data.avg_candidate_per_job
                this.newCandidates.labels = res.data.new_candidates_by_job.map(i => i.name)
                this.newCandidates.dataSet = this.genChartData(res.data.new_candidates_by_job.map(i => ({ value: i.new_applicants })))
                this.activeJobByMonth.labels = res.data.active_job_by_month.map(i => i.month)
                this.activeJobByMonth.dataSet = this.genChartData(res.data.active_job_by_month.map(i => ({ value: i.active_jobs })))
            })
        },
    },
    mounted() {
        this.preloader = true
        Promise.all([this.getJobOverview()]).finally(() => this.preloader = false)
    }
}
</script>