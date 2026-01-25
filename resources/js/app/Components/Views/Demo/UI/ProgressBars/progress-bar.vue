<template>
    <div class="progress-bar-wrapper">
    	<div :class="`progress height-15 radius-20 ${parentClass}`" v-if="type === 'horizontal'">
        	<div :class="`progress-bar ${bgClass}`"
            	role="progressbar"
            	:style="`width: ${percentage}%`">
        		{{ showPercentage ? `${percentage}%` : '' }}
        	</div>
    	</div>
		<div :class="`vertical-progress-bar-wrapper width-1 ${parentClass}`" v-else-if="type === 'vertical'">
        	<div :class="`progress-bar ${bgClass}`"
            	role="progressbar"
            	:style="`height: ${percentage}%`">
				<span class="percentage">
        			{{ showPercentage ? `${percentage}%` : '' }}
				</span>
        	</div>
    	</div>
    	<p v-else class="text-danger">Invalid type</p>
    </div>
</template>

<script>
export default {
	name:"progress-bar",
	props: {
		type: {
			type: String,
			default: 'horizontal'
		},
		showPercentage: {
			type: Boolean,
			default: false
		},
		percentage: {
			type: Number,
			required: true,
		},
		parentClass: {
			type: String,
			default: ''
		},
		bgClass: {
			type: String,
			default: ''
		}
	},
}
</script>

<style lang="scss" scoped>
.vertical-progress-bar-wrapper {
	width: 1rem;
	height: 25rem;
	background-color: #eee;
	border-radius: 3rem;
	overflow: hidden;

	.vertical-progress-bar {
		width: 100%;
	}
	.percentage {
		transform: rotate(90deg);
	}
}

@for $i from 1 through 100 {
 	.vertical-progress-bar-wrapper.width-#{$i} {
    	width: #{$i}rem !important;
  	}
}
</style>
