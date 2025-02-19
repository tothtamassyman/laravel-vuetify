<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['sort_by', 'sort_order', 'search', 'name', 'description', 'users']);

        $condition = $request->input('condition', 'and');

        $query = Group::query()->with(['users'])
            ->filter($filters, $condition);

        $groups = $this->getPaginateAwareResults($request, $query);

        return response()->json($groups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GroupRequest $request
     * @return JsonResponse
     */
    public function store(GroupRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $groupData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        $group = Group::create($groupData);

        if ($request->filled('users')) {
            $userIds = collect($request->input('users'))->pluck('id')->toArray();
            $group->users()->sync($userIds);
        } else {
            $group->users()->sync([]);
        }
        $group->touch();

        return response()->json([
            'success' => true,
            'message' => __('messages.group.created'),
            'group' => $group->load('users'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GroupRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(GroupRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $group = Group::findOrFail($id);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $userIds = collect($request->input('users'))->pluck('id')->toArray();

        $authUser = auth()->user();

        $isUserInGroup = $group->users()->where('users.id', $authUser->id)->exists();

        if ($isUserInGroup && !in_array($authUser->id, $userIds)) {
            return response()->json([
                "errors" => [
                    "users" => [__('messages.group.user_cannot_remove_yourself_from_the_group')]
                ],
            ], 422);
        }

        $group->users()->sync($userIds);
        $group->touch();

        return response()->json([
            'success' => true,
            'message' => __('messages.group.updated'),
            'group' => $group->load('users'),
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
        $group = Group::findOrFail($id);

        if ($group->users()->exists()) {
            return response()->json([
                "message" => [__('messages.group.cannot_delete_group_with_users')],
            ], 403);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.group.deleted')
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
            'message' => __('messages.group.user_removed')
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
                'message' => __('messages.group.not_member')
            ], 403);
        }

        session(['group_id' => $groupId]);

        return response()->json([
            'success' => true,
            'message' => __('messages.group.switched'),
            'group_id' => $groupId
        ]);
    }
}
