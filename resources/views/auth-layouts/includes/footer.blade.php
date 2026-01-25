@stack('before-scripts')
<script>

    window.localStorage.setItem('app-language', '<?php echo app()->getLocale() ?? "en"; ?>');

    window.localStorage.setItem('app-languages',
        JSON.stringify(
            <?php echo json_encode(include resource_path().DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.(app()->getLocale() ?? 'en').DIRECTORY_SEPARATOR.'default.php')?>
        )
    );

    window.appLanguage = window.localStorage.getItem('app-language');
    window.localStorage.setItem('base_url', '{{request()->root()}}');
    // window.localStorage.setItem('s3_url', 'https://abid-bucket-gain.s3.ap-northeast-1.amazonaws.com');

</script>
{!! script(mix('js/manifest.js')) !!}
{!! script(mix('js/vendor.js')) !!}
{!! script(mix('js/core.js')) !!}
@stack('after-scripts')
