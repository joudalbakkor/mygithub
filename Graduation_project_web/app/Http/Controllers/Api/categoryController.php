<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $categories = Category::all(['name', 'image']); 
        return response()->json([
            'categories' => $categories
        ], 200);
    }
    

    public function stores(Category $category)
    {
        return $category->stores()->select('name', 'image', 'rating')->get();
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        try {
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
     
            // Create Category
            Category::create([
                'name' => $request->name,
                'image' => $imageName,
            ]);
     
            // Save Image in Storage folder
            Storage::disk('public')->put($imageName, file_get_contents($request->image));
     
            // Return Json Response
            return response()->json([
                'message' => "Category successfully created."
            ],200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       // category Detail 
       $category = Category::find($id);
       if(!$category){
         return response()->json([
            'message'=>'Category Not Found.'
         ],404);
       }
     
       // Return Json Response
       return response()->json([
          'category' => $category
       ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        try {
            // Find category
            $category = Category::find($id);
            if(!$category){
              return response()->json([
                'message'=>'Category Not Found.'
              ],404);
            }
     
            $category->name = $request->name;

            if($request->image) {
                // Public storage
                $storage = Storage::disk('public');
     
                // Old iamge delete
                if($storage->exists($category->image))
                    $storage->delete($category->image);
     
                // Image name
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $category->image = $imageName;
     
                // Image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }
     
            // Update category
            $category->save();
     
            // Return Json Response
            return response()->json([
                'message' => "Category successfully updated."
            ],200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Detail 
        $category = Category::find($id);
        if(!$category){
          return response()->json([
             'message'=>'Category Not Found.'
          ],404);
        }
     
        // Public storage
        $storage = Storage::disk('public');
     
        // Iamge delete
        if($storage->exists($category->image))
            $storage->delete($category->image);
     
        // Delete category
        $category->delete();
     
        // Return Json Response
        return response()->json([
            'message' => "Category successfully deleted."
        ],200);
    }
}