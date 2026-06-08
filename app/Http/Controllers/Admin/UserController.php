<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->orderBy('name')
                ->orderBy('username')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(['role' => User::ROLE_MANAJER, 'is_active' => true]),
            'roles' => User::roleLabels(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated() + ['is_active' => true]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::roleLabels(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->user()->is($user)) {
            $validated['role'] = $user->role;
            $validated['is_active'] = true;
        }

        if (blank($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        abort_if(request()->user()->is($user), 422, 'Admin tidak dapat menonaktifkan akun sendiri.');

        $user->update(['is_active' => false]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil dinonaktifkan.');
    }
}
