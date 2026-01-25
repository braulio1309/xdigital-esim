@extends('auth-layouts.auth')

@section('title', trans('default.verify'))
@section('contents')
    <div id="app">
        <user-verified></user-verified>
    </div>
    <script>
        window.localStorage.setItem('app-languages',
            JSON.stringify(
                <?php echo json_encode(include resource_path() . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . (app()->getLocale() ?? 'en') . DIRECTORY_SEPARATOR . 'default.php')?>
            )
        );
    </script>
@endsection
