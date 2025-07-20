<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dice;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    protected $roleHierarchy = ['super', 'master', 'admin', 'user', 'staff', 'viewer'];



    public function index()
    {
        if (!auth()->user()->can('Xem danh sách user')) {
            return redirect()->route('dice.index')
                ->with('error', 'Bạn không có quyền truy cập danh sách user.');
        }

        $currentUser = auth()->user();

        $isStaff = false;

        $query = User::query();

        if ($currentUser->staff_for) {
            $currentUser = User::find($currentUser->staff_for);
            $isStaff = true;
        }

        if ($currentUser->hasRole('super')) {
            $query->where('super_id', $currentUser->id);
        } elseif ($currentUser->hasRole('master')) {
            $query->where('master_id', $currentUser->id);
        } elseif ($currentUser->hasRole('admin')) {
            $query->where('admin_id', $currentUser->id);
        } elseif ($currentUser->hasRole('user')) {
            $query->where('user_id', $currentUser->id);
        }

        if ($isStaff) {
            $query->where(function ($q) use ($currentUser) {
                $q->whereNull('staff_for')
                    ->orWhere('staff_for', '!=', $currentUser->id);
            });
        }

        $sessions = $query->with('roles')->paginate(10);

        return view('users.index', compact('sessions'));
    }

    public function create()
    {
        if (!auth()->user()->can('Tạo user')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền tạo user.');
        }
        $currentUser = Auth::user();
        $currentRole = $currentUser->getRoleNames()->first();

        $index = array_search($currentRole, $this->roleHierarchy);
        $lowerRoles = array_slice($this->roleHierarchy, $index + 1);

        $roles = Role::whereIn('name', $lowerRoles)->get(['id', 'name']);

        $permissions = Permission::all();

        $accessibleDiceTables = $currentUser->diceTables()->with('diceParent')->get();

        $diceTablesGroupedByParent = $accessibleDiceTables->groupBy(function ($table) {
            return $table->diceParent->id ?? null; // hoặc $table->diceParent->name nếu muốn theo tên
        });


        // dd($diceTablesGroupedByParent);

        return view('users.create', compact(
            'roles',
            'permissions',
            'diceTablesGroupedByParent'
        ));



        // return view('users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Tạo user')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền tạo user.');
        }
        $validated = $request->validate([
            'name'        => 'required',
            'email'       => 'email',
            'username'    => 'required|string|unique:users,username',
            'password'    => 'required|min:6',
            'role'        => 'required|exists:roles,name',
            'permissions' => 'array',
        ]);

        $currentUser = Auth::user();

        if (!empty($validated['permissions'])) {
            $invalidPermissions = collect($validated['permissions'])->filter(function ($perm) use ($currentUser) {
                return !$currentUser->can($perm);
            });

            if ($invalidPermissions->isNotEmpty()) {
                // Ném lỗi và hiển thị trên view
                throw ValidationException::withMessages([
                    'permissions' => ['Bạn không thể gán các quyền không sở hữu: ' . $invalidPermissions->implode(', ')],
                ]);
            }
        }

        // Gán mặc định
        $managerData = [
            'super_id'  => null,
            'master_id' => null,
            'admin_id'  => null,
            'user_id'   => null,
            'staff_for' => null,
        ];

        if ($currentUser->hasRole('super')) {
            $managerData['super_id'] = $currentUser->id;
        }

        if ($currentUser->hasRole('master')) {
            $managerData['super_id']  = $currentUser->super_id;
            $managerData['master_id'] = $currentUser->id;
        }

        if ($currentUser->hasRole('admin')) {
            $managerData['super_id']  = $currentUser->super_id;
            $managerData['master_id'] = $currentUser->master_id;
            $managerData['admin_id']  = $currentUser->id;
        }

        if ($currentUser->hasRole('user')) {
            $managerData['super_id']  = $currentUser->super_id;
            $managerData['master_id'] = $currentUser->master_id;
            $managerData['admin_id']  = $currentUser->admin_id;
            $managerData['user_id']   = $currentUser->id;
        }

        if ($validated['role'] === 'staff') {
            $managerData['staff_for'] = $currentUser->id;
        }

        $user = User::create([
            'name'      => $validated['name'],
            // 'email'     => $validated['nullable|email'],
            'username'  => $validated['username'],
            'password'  => bcrypt($validated['password']),
            'super_id'  => $managerData['super_id'],
            'master_id' => $managerData['master_id'],
            'admin_id'  => $managerData['admin_id'],
            'expired_at' => $request['expired_at'],
            'user_id'   => $managerData['user_id'],
            'staff_for' => $managerData['staff_for'],
        ]);

        $user->assignRole($validated['role']);

        if (!empty($validated['permissions'])) {
            $user->givePermissionTo($validated['permissions']);
        }


        if ($request->has('dice_tables')) {
            $user->diceTables()->sync(array_keys($request->input('dice_tables')));
        }

        $uniqueParentIds = $user->diceTables
            ->pluck('parent_id')   // lấy toàn bộ parent_id
            ->unique()             // lọc trùng
            ->values();

        if ($uniqueParentIds->isNotEmpty()) {
            $user->dices()->sync($uniqueParentIds->toArray());
        }


        return redirect('/users')->with('success', 'Tạo user thành công');
    }


    public function show(User $user)
    {
        return $user->load('roles');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->can('Cập nhật user')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền cập nhật user.');
        }
        $roles = Role::all();
        $permissions = Permission::all();
        $userPermissions = $user->getPermissionNames();

        $currentUser = Auth::user();

        $accessibleDiceTables = $currentUser->diceTables()->with('diceParent')->get();

        $checkedDiceTableIds = $user->diceTables()->with('diceParent')->get()->pluck('id')->toArray();

        $diceTablesGroupedByParent = $accessibleDiceTables->groupBy(function ($table) {
            return $table->diceParent->id ?? null;
        });


        return view('users.edit', compact('user', 'roles', 'permissions', 'userPermissions', 'diceTablesGroupedByParent', 'checkedDiceTableIds'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can('Cập nhật user')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền cập nhật user.');
        }
        $validated = $request->validate([
            'name'        => 'required',
            'password'    => 'nullable|min:6',
            'role'        => 'required|exists:roles,name',
            'permissions' => 'array',
        ]);

        $currentUser = Auth::user();
        if (!empty($validated['permissions'])) {
            $invalidPermissions = collect($validated['permissions'])->filter(function ($perm) use ($currentUser) {
                return !$currentUser->can($perm);
            });

            if ($invalidPermissions->isNotEmpty()) {
                // Ném lỗi và hiển thị trên view
                throw ValidationException::withMessages([
                    'permissions' => ['Bạn không thể gán các quyền không sở hữu: ' . $invalidPermissions->implode(', ')],
                ]);
            }
        }


        $user->update([
            'name'  => $validated['name'],
            'expires_at'=> $request['expires_at'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
        ]);



        $user->syncRoles([$validated['role']]);

        $user->syncPermissions($validated['permissions'] ?? []);

        if ($request->has('dice_tables')) {
            $user->diceTables()->sync(array_keys($request->input('dice_tables')));
        }

        $uniqueParentIds = $user->diceTables
            ->pluck('parent_id')   // lấy toàn bộ parent_id
            ->unique()             // lọc trùng
            ->values();

        if ($uniqueParentIds->isNotEmpty()) {
            $user->dices()->sync($uniqueParentIds->toArray());
        }


        return redirect()->route('users.index')->with('success', 'Cập nhật user thành công');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->can('Xóa user')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền xóa user.');
        }
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Không thể xóa chính mình.');
        }

        $currentUser = auth()->user();
        if ($currentUser->staff_for) {
            $currentUser = User::find($currentUser->staff_for);
            $isStaff = true;
        }

        $canDelete = false;
        if ($currentUser->hasRole('super') && $user->super_id == $currentUser->id) {
            $canDelete = true;
        } elseif ($currentUser->hasRole('master') && $user->master_id == $currentUser->id) {
            $canDelete = true;
        } elseif ($currentUser->hasRole('admin') && $user->admin_id == $currentUser->id) {
            $canDelete = true;
        } elseif ($currentUser->hasRole('user') && $user->user_id == $currentUser->id) {
            $canDelete = true;
        }

        if ($canDelete == false) {
            return redirect()->route('users.index')->with('false', 'Xóa người dùng thành công.');
        }


        $user->delete();

        return redirect()->route('users.index')->with('success', 'Xóa người dùng thành công.');
    }
}
