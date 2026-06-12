<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffProfile::with('user')->where('is_active', true);
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        $staff = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'role'        => 'required|in:SM,PIC,Staff',
            'employee_id' => 'required|string|max:20|unique:staff_profiles,employee_id',
            'position'    => 'required|in:Store Manager,PIC,Senior Staff,Junior Staff',
            'shift_type'  => 'required|in:FT,DW',
            'phone'       => 'nullable|string|max:20',
            'join_date'   => 'nullable|date',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        StaffProfile::create([
            'user_id'     => $user->id,
            'employee_id' => $request->employee_id,
            'name'        => $request->name,
            'position'    => $request->position,
            'shift_type'  => $request->shift_type,
            'phone'       => $request->phone,
            'join_date'   => $request->join_date,
        ]);

        return redirect()->route('staff.index')
            ->with('success', "Staff {$request->name} berhasil ditambahkan. Email: {$request->email}");
    }

    public function destroy(StaffProfile $staff)
    {
        $staff->update(['is_active' => false]);
        return back()->with('success', 'Staff berhasil dinonaktifkan.');
    }

    /**
     * Reset password staff — hanya SM
     */
    public function resetPassword(Request $request, StaffProfile $staff)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $staff->user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', "Password {$staff->name} berhasil direset.");
    }
}
