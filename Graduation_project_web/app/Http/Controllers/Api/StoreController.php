<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use App\Models\product;
use App\Models\ExternalService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::select('name', 'image', 'rating')->get();

        return response()->json($stores);
    }
 


        public function show($id)
        {
            $store = Store::with(['product', 'externalService', 'seller'])->find($id);
            
            if (!$store) {
                return response()->json(['message' => 'Store not found'], 404);
            }
            
            $data = [
                'name' => $store->name,
                'address' => $store->address,
                'phone' => $store->phone,
                'facebook_link' => $store->facebook_link,
                'instagram_link' => $store->instagram_link,
                'owner' => $store->seller->name
            ];
    
            if ($store->type === 'product' && $store->product) {
                $data['description'] = $store->product->description;
                $data['image1'] = $store->product->image1;
                $data['image2'] = $store->product->image2;
                $data['image3'] = $store->product->image3;
                $data['image4'] = $store->product->image4;
                $data['price'] = $store->product->price;
            } elseif ($store->type === 'service' && $store->externalService) {
                $data['description'] = $store->externalService->description;
                $data['image1'] = $store->externalService->image1;
                $data['image2'] = $store->externalService->image2;
                $data['image3'] = $store->externalService->image3;
                $data['image4'] = $store->externalService->image4;
                $data['price'] = $store->externalService->price;
            } else {
                $data['description'] = 'No product or external service associated with this store.';
                $data['price'] = null;
            }
            
    
            return response()->json($data);
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
    public function store(StoreRequest $request)
    {
        try {
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
     
            // Create Product
            Store::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'facebook_link' => $request->facebook_link,
                'instagram_link' => $request->instagram_link,
                'rating' => $request->rating,
                'seller_id' => $request->seller_id,
                'category_id' => $request->category_id,
                'product_id' => $request->product_id,
                'external_service_id' => $request->external_service_id,
                'image' => $imageName,
            ]);
     
            // Save Image in Storage folder
            Storage::disk('public')->put($imageName, file_get_contents($request->image));
     
            // Return Json Response
            return response()->json([
                'message' => "Store successfully created."
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
    public function update(StoreRequest $request, $id)
    {
        try {
            // Find store
            $store = Store::find($id);
            if(!$store){
              return response()->json([
                'message'=>'Store Not Found.'
              ],404);
            }

            $store->name = $request->name;
            $store->address = $request->address;
            $store->phone = $request->phone;
            $store->facebook_link = $request->facebook_link;
            $store->instagram_link = $request->instagram_link;
            $store->rating = $request->rating;
            if($request->image) {
                // Public storage
                $storage = Storage::disk('public');
     
                // Old iamge delete
                if($storage->exists($store->image))
                    $storage->delete($store->image);
     
                // Image name
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $store->image = $imageName;
     
                // Image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }
     
            // Update store
            $store->save();
     
            // Return Json Response
            return response()->json([
                'message' => "Store successfully updated."
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
        $store = Store::find($id);
        if(!$store){
          return response()->json([
             'message'=>'Store Not Found.'
          ],404);
        }
     
        // Public storage
        $storage = Storage::disk('public');
     
        // Iamge delete
        if($storage->exists($store->image))
            $storage->delete($store->image);
     
        // Delete store
        $store->delete();
     
        // Return Json Response
        return response()->json([
            'message' => "Store successfully deleted."
        ],200);
    }
}
