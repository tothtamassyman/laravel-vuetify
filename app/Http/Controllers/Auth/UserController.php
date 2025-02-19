<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['sort_by', 'sort_order', 'search', 'name', 'description', 'groups', 'roles', 'permissions']);

        $condition = $request->input('condition', 'and');

        $query = User::query()->with([ 'groups', 'roles', 'permissions'])
            ->filter($filters, $condition);

        $permissions = $this->getPaginateAwareResults($request, $query);

        return response()->json($permissions);
    }
}