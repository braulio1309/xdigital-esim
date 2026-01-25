<template>
    <div class="skeleton">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
            loaderColor: {
                type: String,
                default: 'brand-color'
            }
        }
    }
</script>

<style scoped lang="scss">
%loading-skeleton {
    color: transparent !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    background-color: var(--default-border-color) !important;
    border-color: var(--default-border-color) !important;

    &::placeholder {
        color: transparent !important;
    }
}
@keyframes loading-skeleton {
    from {
        opacity: .4;
    }
    to {
        opacity: 1;
    }
}
.skeleton {
    pointer-events: none;
    animation: loading-skeleton 1s infinite alternate;
    
    img {
        filter: grayscale(100) contrast(0%) brightness(1.8);
    }
    label, h1, h2, h3, h4, h5, h6, span,
    p, li,
    .h1, .h2, .h3, .h4, .h5, .h6, .label,
    .text-muted, .text-primary, .text-secondary, .text-info, .text-warning, .text-danger,
    .btn, .badge,
    .form-control {
        @extend %loading-skeleton;
    }
}
</style>
