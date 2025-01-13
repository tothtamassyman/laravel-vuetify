<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->validatePasswordValidationPolicies();

        $this->validateEmailValidationPolicies();

        // Prevent lazy loading of models
        Model::preventLazyLoading();
    }

    /**
     * Validate the password validation policies.
     *
     * @return void
     */
    public function validatePasswordValidationPolicies(): void
    {
        $passwordConfig = config('validationPolicies.password');

        if ($passwordConfig['min_length'] < 1) {
            throw new InvalidArgumentException('The min_length configuration must be an integer greater than 0.');
        }

        if ($passwordConfig['max_length'] < $passwordConfig['min_length']) {
            throw new InvalidArgumentException('The max_length configuration must be an integer greater than or equal to min_length.');
        }

        $booleanKeys = [
            'mixed_case', 'numbers', 'symbols', 'letters', 'uncompromised', 'history_check',
        ];

        foreach ($booleanKeys as $key) {
            if (!is_bool($passwordConfig[$key])) {
                throw new InvalidArgumentException("The {$key} configuration must be a boolean.");
            }
        }
    }

    /**
     * Validate the email validation policies.
     *
     * @return void
     */
    public function validateEmailValidationPolicies(): void
    {
        $emailConfig = config('validationPolicies.email');

        if ($emailConfig['min_length'] < 1) {
            throw new InvalidArgumentException('The min_length configuration must be an integer greater than 0.');
        }

        if ($emailConfig['max_length'] < $emailConfig['min_length']) {
            throw new InvalidArgumentException('The max_length configuration must be greater than or equal to min_length.');
        }

        $allowedStandards = ['rfc', 'strict', 'dns', 'spoof', 'filter', 'filter_unicode'];
        $validationStandards = $emailConfig['validation_standards'];

        foreach ($validationStandards as $key => $value) {
            if (!in_array($key, $allowedStandards, true)) {
                throw new InvalidArgumentException("Invalid validation standard '{$key}' in email configuration.");
            }

            if (!is_bool($value)) {
                throw new InvalidArgumentException("The validation standard '{$key}' must be a boolean.");
            }
        }
    }
}
