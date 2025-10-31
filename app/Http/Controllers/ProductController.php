<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $randomNumber = random_int(100000000000, 999999999999);
        $request->validate([
            'name' => 'required|unique:products,name',
            'price' => 'required',            
            'image' => 'required|image:mimes:jpeg,png,jpg,gif,svg',
        ]);

        $product_data = $request->all();
        $image_name = $request->name.'_'.time().'.'.$request->image->extension();
        $product_data['image'] = $image_name;
        $product_data['upc'] = $randomNumber;
        $pid = Product::create($product_data);
        $request->image->move(public_path('uploads/product/').$pid->id, $image_name);

        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
