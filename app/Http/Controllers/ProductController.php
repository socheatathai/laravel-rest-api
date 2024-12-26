<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Apply filters if provided
        if ($request->has('name')) { // it work as we search name of user
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->query('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->query('max_price'));
        }

        if ($request->has('quantity')) {
            $query->where('quantity', $request->query('quantity'));
        }

        // Apply sorting if provided
        $orderBy = $request->query('orderby', 'id'); // Default order by 'id'
        $orderDirection = $request->query('direction', 'asc'); // Default order direction 'asc'
        $query->orderBy($orderBy, $orderDirection);

        $products = $query->get();

        if ($products->count() == 0) {
            return response()->json(["message" => "No Products Found"], 404);
        }

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => false,
            ], 400);
        }

        $user = new Product;
        $user->name = $request->name;
        $user->description = $request->description;
        $user->price = $request->price;
        $user->save();

        return response()->json([
            'message' => 'Products added successfully',
            'status' => true,
            'data' => $user,
        ], 200);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message"=> "Product not found"],404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message"=> "Product not found"],404);
        }
        $product->update($request->all());
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message"=> "Product No Found"],404);
        }
        $product->delete();
        return response()->json($product,200, ["message"=> "Product deleted successfully"]);
    }
    
}
