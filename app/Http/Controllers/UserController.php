<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request){
        $perPage = $request->get('per_page', 10); // Default to 10 items per page
        $users = User::orderBy('name', 'ASC')->paginate($perPage);
        return response()->json([
            'message' => $users->total() . ' users found',
            'data' => $users->items(),
            'status' => true,
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
            ],
        ], 200);
    }

    public function show($id){
      
        $user = User::find($id);

        if($user != null){
            return response()->json([
                'message' => $user->name.' User found',
                'data' => $user,
                'status' => true,
            ], 200);
        }else{
            return response()->json([
                'message' => 'Record not found',
                'status' => true,
                'data' => [],
            ], 200);

        }
    }
    public function me(Request $request){
        $user = User::find($request->user_id);
        if($user != null){
            return response()->json([
                'message' => $user->name.' User found',
                'data' => $user,
                'status' => true,
            ], 200);
        }else{
            return response()->json([
                'message' => 'Record not found',
                'status' => true,
                'data' => [],
            ], 200);
        }
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Please fix the errors',
                'errors' => $validator->errors(),
                'status' => false,
            ], 200);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json([
            'message' => 'User added successfully',
            'status' => true,
            'data' => $user,
        ], 200);
    }

    public function update(Request $request, $id){
        $user = User::find($id);


        if($user == null){
            return response()->json([
                'message' => 'User not found',
                'status' => false,
            ], 200);
        }

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
         ];

         $validator = Validator::make($request->all(),$rules);
         if($validator->fails()){
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors(),
            ], 200);
         }

         $user->name = $request->name;
         $user->email = $request->email;
         $user->password = $request->password;
         $user->save();

         return response()->json([
            'message' => 'User updated successfully',
            'status' => true,
            'data' => $user,
         ], 200);

    }


    public function destroy($id){
        $user = User::find($id);
        if($user == null){
            return response()->json([
                'message' => 'User not found',
                'status' => false,
            ], 200);
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully',
            'status' => true,
        ], 200);
    }

    public function upload(Request $request){

        if (!$request->has('image')) {
            return response()->json([
                'message' => 'No image provided',
                'status' => false,
            ], 400);
        }
    
        if (empty($request->all())) {
            return response()->json([
                'message' => 'No data provided',
                'status' => false,
            ], 400);
        }

        $validator = Validator::make($request->all(),[
            'image' => 'required|mimes:png,jpg,jpeg,gif',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $img = $request->image;
        $getExt = $img->getClientOriginalExtension();
        $imgName = time().'.'.$getExt;
        $img->move(public_path().'/uploads', $imgName);

        $image = new Image;
        $image->image = $imgName;
        $image->save();

        return response()->json([
            'status' => true,
            'path' => asset('/uploads/'.$imgName),
            'message' => 'image uploaded successfully',
            'data' => $image,
        ]);


    }

    public function delete($id){
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:images,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors(),
            ], 400);
        }
    
        $image = Image::find($id);
    
        if (!$image) {
            return response()->json([
                'message' => 'Image not found',
                'status' => false,
            ], 404);
        }
    
        $imagePath = public_path('/uploads/' . $image->image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    
        $image->delete();
    
        return response()->json([
            'message' => 'Image deleted successfully',
            'status' => true,
        ], 200);
    }
}

