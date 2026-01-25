@extends('auth-layouts.auth')

@section('title', trans('default.registration'))
@section('contents')
    <div id="app">
        <registration site-key="{{ $site_key }}"
               recaptcha-enable="{{ $recaptcha_enable }}"
               :config-data="{{ json_encode(config('settings.application')) }}"
        ></registration>
    </div>
    <script>
        window.localStorage.setItem('app-languages',
            JSON.stringify(
                <?php echo json_encode(include resource_path() . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . (app()->getLocale() ?? 'en') . DIRECTORY_SEPARATOR . 'default.php')?>
            )
        );
    </script>
@endsection
