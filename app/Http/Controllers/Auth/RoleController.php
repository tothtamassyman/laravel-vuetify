<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['sort_by', 'sort_order', 'search', 'name', 'description', 'guard_name', 'permissions']);

        $condition = $request->input('condition', 'and');

        $query = Role::query()->with(['permissions.conditions', 'permissions.fields'])
            ->filter($filters, $condition);

        $roles = $this->getPaginateAwareResults($request, $query);

        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RoleRequest  $request
     * @return JsonResponse
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $roleData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => $validated['guard_name'],
        ];

        $role = Role::create($roleData);

        if ($request->filled('permissions')) {
            $permissionIds = collect($request->input('permissions'))->pluck('id')->toArray();
            $role->syncPermissions($permissionIds);
        } else {
            $role->syncPermissions([]);
        }
        $role->touch();

        return response()->json([
            'success' => true,
            'message' => __('messages.role.created'),
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RoleRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function update(RoleRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $role = Role::findOrFail($id);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => $validated['guard_name'],
        ]);

        if ($request->filled('permissions')) {
            $permissionIds = collect($request->input('permissions'))->pluck('id')->toArray();
            $role->syncPermissions($permissionIds);
        } else {
            $role->syncPermissions([]);
        }
        $role->touch();

        return response()->json([
            'success' => true,
            'message' => __('messages.role.updated'),
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.role.deleted')
        ]);
    }
}
