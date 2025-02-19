<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Models\PermissionCondition;
use App\Models\PermissionField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['sort_by', 'sort_order', 'search', 'name', 'description', 'guard_name', 'fields', 'conditions']);

        $condition = $request->input('condition', 'and');

        $query = Permission::query()->with(['conditions', 'fields'])
            ->filter($filters, $condition);

        $permissions = $this->getPaginateAwareResults($request, $query);

        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PermissionRequest  $request
     * @return JsonResponse
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $permissionData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => $validated['guard_name'],
        ];

        $permission = Permission::create($permissionData);

        $this->syncFieldsAndConditions($permission, $validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.permission.created'),
            'permission' => $permission->load(['fields', 'conditions']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PermissionRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function update(PermissionRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $permission = Permission::findOrFail($id);

        $permission->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => $validated['guard_name'] ?? null,
        ]);

        $this->syncFieldsAndConditions($permission, $validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.permission.updated'),
            'permission' => $permission->load(['fields', 'conditions']),
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
        $permission = Permission::findOrFail($id);

        $permission->fields()->detach();
        $permission->conditions()->detach();
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.permission.deleted')
        ]);
    }

    /**
     * Sync fields and conditions for the permission.
     *
     * @param  Permission  $permission
     * @param  array  $validated
     * @return void
     */
    private function syncFieldsAndConditions(Permission $permission, array $validated): void
    {
        $fieldIds = [];
        if (!empty($validated['fields'])) {
            foreach ($validated['fields'] as $fieldName) {
                $field = PermissionField::updateOrCreate(['field' => $fieldName]);
                $fieldIds[] = $field->id;
            }
        }
        $permission->fields()->sync($fieldIds);

        $conditionIds = [];
        if (!empty($validated['conditions'])) {
            foreach ($validated['conditions'] as $condition) {
                $conditionModel = PermissionCondition::updateOrCreate([
                    'key' => $condition['key'],
                    'operator' => $condition['operator'],
                    'value' => $condition['value']
                ]);
                $conditionIds[] = $conditionModel->id;
            }
        }
        $permission->conditions()->sync($conditionIds);

        $this->deleteUnusedFieldsAndConditions();
    }

    /**
     * Delete unused fields and conditions.
     *
     * @return void
     */
    private function deleteUnusedFieldsAndConditions(): void
    {
        PermissionField::whereDoesntHave('permissions')->delete();
        PermissionCondition::whereDoesntHave('permissions')->delete();
    }
}
