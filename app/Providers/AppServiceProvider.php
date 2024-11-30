<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordComplexity;
use App\Services\SecurityLogger;


class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SecurityLogger::class, function ($app) {
            return new SecurityLogger();
        });
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('password_complexity', function($attribute, $value, $parameters, $validator) {
            return (new PasswordComplexity)->passes($attribute, $value);
        });

        Validator::replacer('password_complexity', function($message, $attribute, $rule, $parameters) {
            return 'Password must be at least 8 characters long, including uppercase letters, lowercase letters, numbers and special characters.';
        });
    }
}
