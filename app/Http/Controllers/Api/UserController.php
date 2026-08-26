<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::select(
                'id',
                'name',
                'email',
                'role'
            )->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in([
                    User::ROLE_ADMINISTRATOR,
                    User::ROLE_LOAN_OFFICER,
                ]),
            ],
        ]);

        $user = User::create($data);

        return response()->json([
            'data' => $this->userData($user),
        ], 201);
    }

    public function update(
        Request $request,
        User $user
    ): JsonResponse {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'role' => [
                'sometimes',
                Rule::in([
                    User::ROLE_ADMINISTRATOR,
                    User::ROLE_LOAN_OFFICER,
                ]),
            ],
        ]);

        $user->update($data);

        return response()->json([
            'data' => $this->userData($user),
        ]);
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}