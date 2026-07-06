<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Helpers\App\Traits\ReCaptchaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\LoginRequest as Request;
use App\Services\Core\Auth\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use App\Hooks\User\CustomRoute;
class LoginController extends Controller
{
    use ReCaptchaHelper;
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }


    public function show()
    {
        $recaptcha = $this->getReCaptcha();
        return view('auth.login', $recaptcha);
    }
    /* From Core
    public function show()
    {
        return env('APP_INSTALLED') ? view('auth.login') : redirect('install');
    }
    */
    /**
     * @param Request $request
     * @return string
     */
    public function login(Request $request)
    {
        try {
            $user = $this->service->login();
            $code = $this->service->createTwoFactorChallenge($user, $request->get('remember_me', false));

            try {
                $this->service->sendTwoFactorCode($user, $code);
            } catch (\Exception $exception) {
                $this->service->clearTwoFactorChallenge();
                throw $exception;
            }

            return response()->json([
                'two_factor_required' => true,
                'message' => trans('default.two_factor_code_sent')
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception instanceof ModelNotFoundException ? trans('default.resource_not_found', ['resource' => trans('default.user')]) : $exception->getMessage()
            ], 400);
        }
    }

    public function verifyLoginCode(HttpRequest $request)
    {
        try {
            $this->service->verifyTwoFactorCode($request->get('code'));

            // custom hook
            $route = CustomRoute::new(true)->handle();
            $route = count($route) ? $route : home_route();

            return route(
                $route['route_name'],
                $route['route_params']
            );
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception instanceof ModelNotFoundException ? trans('default.resource_not_found', ['resource' => trans('default.user')]) : $exception->getMessage()
            ], 400);
        }
    }

    public function logOut(): RedirectResponse
    {
        session()->flush();
        auth()->logout();
        session()->flush();

        return redirect()->route('users.login.index');
    }
}
