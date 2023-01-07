<?php

namespace App\Exceptions;

use App\Mail\ExceptionEmail;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;
use Mail;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Exception|Throwable $e)
    {
        $request = request();
        /** @var User $user */
        $user = auth()->user();
        $userInfo = is_null($user) ? ['no current user' => 'really'] : $user->toArray();
        $ipAddress = $request->ip() ?? 'N/A';
        $xForwardedFor = $request->header('X-Forwarded-For') ?? 'N/A';
        $subject = 'Fungibl Exception: ' . $e->getMessage();
        $data = ['subject' => $subject, 'exception' => $e,];
        $data['email_vars'] = [
            'userInfo' => $userInfo,
            'ipAddress' => $ipAddress,
            'xForwardedFor' => $xForwardedFor,
        ];
        Log::error($e->getMessage(), $data['email_vars']);
        $data['email_vars']['exception'] = $e; // no need to log this here
//        Mail::to('david+exceptions@fungibl.fun')
//            ->send(new ExceptionEmail($data));
        parent::report($e);
    }

}
