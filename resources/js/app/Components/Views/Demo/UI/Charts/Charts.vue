<template>
	<div class="content-wrapper">
        <app-breadcrumb :page-title="$t('charts')" :directory="$t('ui_elements')" :icon="'bar-chart'"/>
		<div class="charts-wrapper">
			<div class="card card-with-shadow p-3 span-full">
				<app-chart class="mb-primary"
            		type="bar-chart"
            		:height="210"
            		:data-sets="barChartProps.dataSets"
            		:labels="barChartProps.labels"
        		/>
			</div>

			<div class="card card-with-shadow p-3 pi-chart span-half">
				<app-chart class="mb-primary"
            		type="pie-chart"
            		:height="210"
            		:data-sets="pieChartProps.dataSets"
            		:labels="pieChartProps.labels"
        		/>
			</div>
			<div class="card card-with-shadow p-3 span-half">
				<app-chart class="mb-primary"
            		type="dough-chart"
            		:height="210"
            		:data-sets="doughnutChartProps.dataSets"
            		:labels="doughnutChartProps.labels"
        		/>
			</div>
			<div class="card card-with-shadow p-3 span-full">
				<app-chart class="mb-primary"
            		type="horizontal-line-chart"
            		:height="210"
            		:data-sets="horizontalChartProps.dataSets"
            		:labels="horizontalChartProps.labels"
        		/>
			</div>
		</div>
    </div>	
</template>

<script>
export default {
	name: "Charts",
	data() {
		return {
			barChartProps: {
				labels: [
        			'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
    			],
    			dataSets: [
        			{
            			label: 'Sales',
            			backgroundColor: '#21252D',
            			data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
        			},
        			{
            			label: 'Purchase',
            			backgroundColor: '#4466F2',
            			data: [20, 40, 32, 49, 20, 50, 19, 30, 70, 20, 42, 21]
        			}
    			]

			},
			pieChartProps: {
				labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],
    			dataSets: [
        			{
            			backgroundColor: [
                			'#f96868',
                			'#4466F2',
                			'#2e383e',
                			'#6a008a'
            			],
            			data: [20, 25, 40, 15]
        			}
    			]  
			},
			doughnutChartProps: {
				labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],
    			dataSets: [
        			{
            			backgroundColor: [
                			'#f96868',
                			'#4466F2',
                			'#2e383e',
                			'#6a008a'
            			],
            			data: [20, 25, 40, 15]
        			}
    			]  
			},
			horizontalChartProps: {
				labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    			dataSets: [
        			{
            			label: 'Data One',
            			backgroundColor: '#4466F2',
            			data: [40, 20, 52, 39, 30, 60, 39, 80, 45, 30, 22, 15]
        			}
    			]
			}
		}
	},
}
</script>

<style lang="scss" scoped>
.charts-wrapper {
	grid-gap: 1.75rem;
	display: grid;
	grid-template-columns: repeat(4, 1fr)	;

	.span-full {
		grid-column: 1 / -1;
	}

	.span-half {
		grid-column: span 2;
	}
}
</style>
