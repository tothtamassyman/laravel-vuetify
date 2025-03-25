<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Group;
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

        $query = User::query()->with(['groups', 'defaultGroup', 'currentGroup', 'roles', 'permissions'])
            ->filter($filters, $condition);

        $users = $this->getPaginateAwareResults($request, $query);

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRequest  $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
//        $userData = [
//            'name' => $validated['name'],
//            'email' => $validated['email'],
//            'password' => bcrypt($validated['password']),
//        ];
//
//        $user = new User($userData);
//        $user->save();
//
//        $user->setDetail('default_group_id', $validated['default_group_id']);
//
//        return response()->json([
//            'success' => true,
//            'message' => __('messages.user.updated'),
//            'user' => $user->load('groups', 'defaultGroup', 'currentGroup', 'roles', 'permissions'),
//        ]);

        $userData = $request->only(['name', 'email', 'password']);
        $user = User::create($userData);

//        $user->setDetail('default_group_id', $request->input(['default_group.id']));

        if ($request->has('details')) {
            $details = $request->input('details');

            foreach ($details as $detailsKey => $detailsValue) {
                $user->setDetail($detailsKey, $detailsValue);
            }

            $user->touch();
        }

        if ($request->has('groups')) {
            $groupIds = $request->input('groups');
            $user->groups()->sync($groupIds);
        }

//        if ($request->has('groups')) {
//            $groupIds = collect($request->input('groups'))->pluck('id')->toArray();
//            $groups = Group::findMany($groupIds);
//            $user->groups()->sync($groups);
//        }

        $user->touch();

        return response()->json([
            'success' => true,
            'message' => __('messages.user.updated'),
            'user' => $user->load('groups', 'defaultGroup', 'currentGroup', 'roles', 'permissions'),
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
        $user = User::findOrFail($id);

        if (auth()->user()->id == $user->id) {
            return response()->json([
                "message" => __('messages.user.cannot_delete_yourself'),
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.user.deleted')
        ]);
    }
}