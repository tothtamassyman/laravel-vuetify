<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $groups = Group::with('users')->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:groups',
            'description' => 'nullable|string'
        ]);

        $group = Group::create($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => __('group.created'),
            'data' => $group
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $group = Group::with('users')->find($id);

        return response()->json([
            'success' => true,
            'data' => $group
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:groups,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $group = Group::findOrFail($id);
        $group->update($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => __('group.updated'),
            'data' => $group
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => __('group.deleted')
        ]);
    }

    /**
     * Add user to the group.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function addUser(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $group = Group::findOrFail($id);
        $group->users()->attach($request->input('user_id'));

        return response()->json([
            'success' => true,
            'message' => __('group.user_added')
        ]);
    }

    /**
     * Remove user from the group.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function removeUser(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $group = Group::findOrFail($id);
        $group->users()->detach($request->input('user_id'));

        return response()->json([
            'success' => true,
            'message' => __('group.user_removed')
        ]);
    }

    /**
     * Switch to a different group for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function switchGroup(Request $request): JsonResponse
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $user = $request->user();
        $groupId = $request->input('group_id');

        if (!$user->groups()->where('id', $groupId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('group.not_member')
            ], 403);
        }

        session(['group_id' => $groupId]);

        return response()->json([
            'success' => true,
            'message' => __('group.switched'),
            'group_id' => $groupId
        ]);
    }
}
