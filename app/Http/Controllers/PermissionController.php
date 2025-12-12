<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Validator;
use DataTables;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            
            $data = Permission::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <span data-id="'.$row->id.'" class="delete_btn btn btn-danger btn-sm">Delete</span>';
                    return $btn;
                })
            ->rawColumns(['action'])
            ->make(true);
        };
        return view('permission.index');
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
        $validator = validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
        ]);

        if( $validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission = Permission::create(['name' => $request->name]);
        return response()->json(['success' => true, 'permission' => $permission->id]);
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
    public function edit(Permission $permission)
    {
        return response()->json(['sucess' => true, 'permission' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = validator::make($request->all(), [
            'name' => 'required',
        ]);

        if( $validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $permission->update($request->all());
        return response()->json(['success' => true, 'permission' => $permission->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission = Permission::find($permission->id);        
        if($permission){
            $permission->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
