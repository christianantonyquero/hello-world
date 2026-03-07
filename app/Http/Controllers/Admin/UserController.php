<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate('25');
        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'unique:users',
        ]);

        $user = new User($request->only('name', 'email'));
        $user->is_admin = $request->is_admin ?? false;
        $user->is_tester = $request->is_tester ?? false;
        $user->password = Hash::make($request->password);
        $user->save();
        return $user;
    }

    public function show(Request $request)
    {
        return $request->user;
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'unique:users,email,'.$request->user->id,
        ]);

        $request->user->fill($request->only('name', 'email', 'is_admin'));
        $request->user->is_admin = $request->is_admin ?? false;
        $request->user->is_tester = $request->is_tester ?? false;
        $request->user->save();

        if($request->password) {
            $request->user->update([
                'password' => Hash::make($request->password),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $request->user->forceDelete();
        return redirect(route('admin.users.index'));
    }

    // Export Users
    public function export(Request $request)
    {
        $now_str = now()->format('Y-m-d');
        return Excel::download(new UsersExport, "users-{$now_str}.csv");
    }
}
