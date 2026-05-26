<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('warehouse:id,name');
        if ($s = $request->input('search')) {
            $query->where(fn($q) => $q->where('name','ilike',"%{$s}%")->orWhere('email','ilike',"%{$s}%"));
        }
        if ($role = $request->input('role')) { $query->where('role', $role); }
        $query->orderBy($request->input('sort','name'), $request->input('direction','asc'));

        return Inertia::render('Users/Index', [
            'users' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search','role','sort','direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'warehouses' => Warehouse::where('is_active',true)->select('id','name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'=>'required|string|max:255','email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:8|confirmed','role'=>'required|in:admin,manager,staff,viewer',
            'warehouse_id'=>'nullable|exists:warehouses,id','is_active'=>'boolean',
        ]);
        $v['password'] = Hash::make($v['password']);
        $user = User::create($v);
        AuditLogService::log('create','users',"Created user: {$user->name} ({$user->role})");
        return redirect()->route('users.index')->with('success','User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user'=>$user, 'warehouses'=>Warehouse::where('is_active',true)->select('id','name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $v = $request->validate([
            'name'=>'required|string|max:255','email'=>"required|email|unique:users,email,{$user->id}",
            'password'=>'nullable|string|min:8|confirmed','role'=>'required|in:admin,manager,staff,viewer',
            'warehouse_id'=>'nullable|exists:warehouses,id','is_active'=>'boolean',
        ]);
        $old = $user->toArray();
        if(!empty($v['password'])){$v['password']=Hash::make($v['password']);}else{unset($v['password']);}
        $user->update($v);
        AuditLogService::log('update','users',"Updated user: {$user->name}",$old,$user->fresh()->toArray());
        return redirect()->route('users.index')->with('success','User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if($user->id===auth()->id()) return back()->with('error','Tidak dapat menghapus akun sendiri.');
        $old=$user->toArray(); $name=$user->name; $user->delete();
        AuditLogService::log('delete','users',"Deleted user: {$name}",$old);
        return redirect()->route('users.index')->with('success','User berhasil dihapus.');
    }
}
