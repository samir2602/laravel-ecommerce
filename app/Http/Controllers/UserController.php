<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use DataTables;
use Validator;

class UserController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [            
            new Middleware('permission:add_user', only: ['index']),
            new Middleware('permission:edit_user', only: ['edit']),
            new Middleware('permission:add_user', only: ['create']),
            new Middleware('permission:delete_user', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $role = Role::all();
        if($request->ajax()){
            $data = User::all();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addcolumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <span data-id="'.$row->id.'" class="delete_btn btn btn-danger btn-sm">Delete</span>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('user.index', compact('role'));
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
        //
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
    public function edit(User $user)
    {
        return response()->json(['success' => true, 'user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = validator::make($request->all(),[
            'name' => 'required|unique:users,name,'.$user->id,
            'email' => 'required|unique:users,email,'.$user->id,
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update(['name' => $request->name, 'email' => $request->email]);
        if($request->has('role')){
            $user->syncRoles($request->role);
        }else{
            $user->syncRoles([]);
        }
        return response()->json(['success' => true, 'role' => $user->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
