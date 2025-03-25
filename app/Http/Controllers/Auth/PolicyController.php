<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PolicyController extends Controller
{
    public function index(): JsonResponse
    {
        $passwordPolicy = [
            'min_length' => config('validationPolicies.password.min_length'),
            'max_length' => config('validationPolicies.password.max_length'),
            'mixed_case' => config('validationPolicies.password.mixed_case'),
            'letters' => config('validationPolicies.password.letters'),
            'numbers' => config('validationPolicies.password.numbers'),
            'symbols' => config('validationPolicies.password.symbols'),
            'uncompromised' => config('validationPolicies.password.uncompromised'),
            'history_check' => config('validationPolicies.password.history_check'),
            'history_limit' => config('validationPolicies.password.history_limit'),
        ];

        $emailPolicy = [
            'min_length' => config('validationPolicies.email.min_length'),
            'max_length' => config('validationPolicies.email.max_length'),
            'validation_standards' => [
                'rfc' => config('validationPolicies.email.validation_standards.rfc'),
                'strict' => config('validationPolicies.email.validation_standards.strict'),
                'filter' => config('validationPolicies.email.validation_standards.filter'),
                'filter_unicode' => config('validationPolicies.email.validation_standards.filter_unicode'),
            ],
        ];

        return response()->json([
            'status' => 'success',
            'password' => $passwordPolicy,
            'email' => $emailPolicy,
        ]);
    }
}