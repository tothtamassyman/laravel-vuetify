<?php

namespace App\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * This exception extends the default ValidationException to improve
 * * error message handling, including "and more" messages.
 */
class EnhancedValidationException extends ValidationException
{
    /**
     * Create an error message summary from the validation errors.
     *
     * @param  Validator  $validator
     * @return string
     */
    protected static function summarize($validator): string
    {
        if (!$validator instanceof Validator) {
            throw new \InvalidArgumentException('Invalid validator instance.');
        }

        $messages = $validator->errors()->all();

        if (empty($messages) || !isset($messages[0]) || !is_string($messages[0])) {
            return __('validation.and_more_invalid_data');
        }

        $message = array_shift($messages);

        if ($count = count($messages)) {
            $message .= ' ('.trans_choice('validation.and_more', $count, ['count' => $count]).')';
        }

        return $message;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => self::summarize($this->validator),
            'errors' => $this->validator->errors(),
        ], 422);
    }
}
