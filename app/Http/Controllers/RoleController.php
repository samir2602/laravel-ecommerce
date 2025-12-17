<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Validator;
use DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $data = Role::with('permissions')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <span data-id="'.$row->id.'" class="delete_btn btn btn-danger btn-sm">Delete</span>';
                    return $btn;
                })
                ->addColumn('permissions', function($row){
                    $permission = $row->permissions->pluck('name')->toArray();
                    return implode(' || ', $permission);
                })
                ->rawColumns(['action', 'permissions'])
                ->make(true);
        }
        $permissions = Permission::all();
        return view('role.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validator = validator::make($request->all(),[
            'name' => 'required|unique:roles,name'
       ]);

       if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
       }

       $role = Role::create(['name' => $request->name ]);
       if($request->has('permissions') ){
            $role->syncPermissions($request->permissions);
       }

       return response()->json(['success' => true, 'role' => $role->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permission = Role::findOrFail($role->id); // Find by ID          
        $role->permission = $permission->permissions->pluck('id')->toArray();
        return response()->json(['success' => true, 'role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = validator::make($request->all(),[
            'name' => 'required|unique:roles,name,'.$role->id
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update(['name' => $request->name]);
        if($request->has('permissions')){
            $role->syncPermissions($request->permissions);
        }else{
            $role->syncPermissions([]);
        }
        return response()->json(['success' => true, 'role' => $role->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role = Role::find($role->id);
        if($role){
            $role->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
