<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller class providing common functionality for all controllers.
 *
 * This class extends Laravel's BaseController and includes useful traits for authorization and validation.
 * It also provides a utility method to handle pagination-aware queries.
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Retrieve paginated or non-paginated query results based on the request parameters.
     *
     * This method checks if pagination parameters (`current_page` and `per_page`) are provided
     * in the request. If they are, it returns paginated results; otherwise, it returns the full dataset.
     *
     * @param  Request  $request  The current HTTP request instance containing query parameters.
     * @param  Builder  $query  The Eloquent query builder instance for retrieving data.
     *
     * @return LengthAwarePaginator|array Returns paginated results when pagination parameters
     *         are present; otherwise, returns an array with all query results.
     */
    protected function getPaginateAwareResults(Request $request, Builder $query): LengthAwarePaginator|array
    {
        // Check if pagination parameters exist in the request
        if ($request->filled('page') && $request->filled('per_page')) {
            // Retrieve pagination values from request, with defaults if not provided
            $currentPage = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 5);

            // Return all results if per_page is set to -1
            if ($perPage === -1) {
                $data = $query->get();

                return [
                    'data' => $data,
                    'pagination' => [
                        'total' => count($data),
                        'from' => 1,
                        'to' => count($data),
                        'per_page' => count($data),
                        'current_page' => 1,
                        'last_page' => 1,
                    ]
                ];
            }

            // Return paginated results including query parameters
            $pagination = $query->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

            // Return paginated results with pagination metadata
            return [
                'data' => $pagination->items(),
                'pagination' => [
                    'total' => $pagination->total(),
                    'from' => $pagination->firstItem(),
                    'to' => $pagination->lastItem(),
                    'per_page' => $pagination->perPage(),
                    'current_page' => $pagination->currentPage(),
                    'last_page' => $pagination->lastPage(),
                ]
            ];
        }

        // If pagination parameters are not provided, return all results
        return [
            'data' => $query->get()
        ];
    }
}
