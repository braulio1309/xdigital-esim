<template>
	<div class="content-wrapper">
        <app-breadcrumb :page-title="$t('progress_bars')" :directory="$t('ui_elements')" :icon="'bar-chart'"/>

		<div class="d-flex mb-3" style="gap: 1rem">
			<div class="card card-with-shadow px-4 pt-3 w-50">
            	<p class="lead mb-5">{{$t('basic_progress_bars')}}</p>

				<div class="mb-2">
					<ProgressBar parent-class="mb-4" :percentage="0"  />
					<ProgressBar parent-class="mb-4" :percentage="25"  />
					<ProgressBar parent-class="mb-4" :percentage="50" />
					<ProgressBar parent-class="mb-4" :percentage="75" :show-percentage="true"/>
					<ProgressBar parent-class="mb-4" :percentage="100" />
				</div>
			</div>
			<div class="w-50 card card-with-shadow px-4 pt-3">
            	<p class="lead mb-5">{{$t('colored_progress_bars')}}</p>

				<div class="mb-2">
					<ProgressBar parent-class="mb-4" bg-class="bg-secondary" :percentage="25" />
					<ProgressBar parent-class="mb-4" bg-class="bg-warning" :percentage="50" />
					<ProgressBar parent-class="mb-4" bg-class="bg-info" :percentage="75" />
					<ProgressBar parent-class="mb-4" bg-class="bg-success" :percentage="100" />
				</div>
			</div>
    </div>
    <div class="d-flex mb-3" style="gap: 1rem">
			<div class="w-50 card card-with-shadow px-4 pt-3">
            	<p class="lead mb-5">{{$t('striped_progress_bars')}}</p>

				<div class="mb-2">
					<ProgressBar parent-class="mb-4" bg-class="bg-secondary progress-bar-striped" :percentage="25" />
					<ProgressBar parent-class="mb-4" bg-class="bg-warning progress-bar-striped" :percentage="50" />
					<ProgressBar parent-class="mb-4" bg-class="bg-info progress-bar-striped" :percentage="75" />
					<ProgressBar parent-class="mb-4" bg-class="bg-success progress-bar-striped" :percentage="100" />
				</div>
			</div>
			<div class="w-50 card card-with-shadow px-4 pt-3">
            	<p class="lead mb-5">{{$t('animated_progress_bars')}}</p>

				<div class="mb-2">
					<ProgressBar parent-class="mb-4" bg-class="bg-secondary progress-bar-striped progress-bar-animated" :percentage="25"  />
					<ProgressBar parent-class="mb-4" bg-class="bg-warning progress-bar-striped progress-bar-animated" :percentage="50" />
					<ProgressBar parent-class="mb-4" bg-class="bg-info progress-bar-striped progress-bar-animated" :percentage="75" />
					<ProgressBar parent-class="mb-4" bg-class="bg-success progress-bar-striped progress-bar-animated" :percentage="100" />
				</div>
			</div>
    </div>
    <div class="d-flex align-content-start" style="gap: 1rem">
			<div class="w-50 card card-with-shadow px-4 pt-3">
            	<p class="lead mb-5">{{$t('different_size_progress_bars')}}</p>

				<div class="mb-2">
					<ProgressBar parent-class="mb-4 height-1" bg-class="bg-success" :percentage="25" />
					<ProgressBar parent-class="mb-4 height-25" bg-class="bg-danger" :percentage="50" />
					<ProgressBar parent-class="mb-4 height-20" bg-class="bg-accent" :percentage="75" />
					<ProgressBar parent-class="mb-4 height-20" bg-class="bg-accent" :percentage="100" />
				</div>
			</div>

			<div class="w-50 card card-with-shadow px-4 pt-3">
            	<p class="lead mb-5">{{$t('vertical_progress_bars')}}</p>

				<div class="mb-2 d-flex flex-wrap" style="gap: 2.5rem;">
					<ProgressBar type="vertical" parent-class="mb-4" :percentage="25" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-secondary" :percentage="50" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-warning" :percentage="75" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-danger progress-bar-striped" :percentage="100" />

					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-info" :show-percentage="true" :percentage="25" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-success" :percentage="50" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-light" :percentage="75" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-dark progress-bar-striped progress-bar-animated" :percentage="100" />

					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-info" :percentage="25" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-success" :percentage="50" />
					<ProgressBar type="vertical" parent-class="mb-4" bg-class="bg-light" :percentage="75" />
				</div>
			</div>
		</div>
    </div>
</template>

<script>
import ProgressBar from './progress-bar.vue';

export default {
	name:"ProgressBars",
	components: {ProgressBar},
}
</script>

<style lang="scss" scoped>

</style>
