<?php

namespace App\Http\Controllers;

use App\Models\ProductMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ProductMasterController extends Controller
{   
    ######### Show List #############
    public function index(Request $request)
    {
        try {
            Log::info('REQUEST COMES FROM TASKS MASTER: ' . $request->getContent());

            // Version Key to track changes
            $versionKey = 'product_version';
            $currentVersion = Redis::get($versionKey) ?? time(); // Use timestamp as default version if not set
            $cacheKey = 'product_filter_' . md5(json_encode($request->input())) . '_v' . $currentVersion;

            // Check if data is already cached
            $cachedProducts = Redis::get($cacheKey);


            if ($cachedProducts) {
                $data = json_decode($cachedProducts);
                if ($data) {
                    return response()->json([
                        'Status' => 200,
                        'TotalRecord' => count($data),
                        'DataList' => $data
                    ]);
                }
            } else {
        $arrayDataRows = array();

        $id = $request->input('Id');
        $Search = $request->input('Name');
        $Status = $request->input('Status');

        $posts = ProductMaster::when($Search, function ($query) use ($Search) {
            return $query->where('ClientName', 'like', '%' . $Search . '%');
        })->when($id, function ($query) use ($id) {
            return $query->where('id',  $id );
        })->when(isset($Status), function ($query) use ($Status) {
             return $query->where('Status',$Status);
        })->select('*')->orderBy('id')->get('*');


        if ($posts->isNotEmpty()) {
            $arrayDataRows = [];
            foreach ($posts as $post){

                $arrayDataRows[] = [
                    "Id" => $post->id,
                    "ClientName" => $post->ClientName,
                    "ProductName" => $post->ProductName,
                    "ProductPrice" => $post->ProductPrice,
                    "Store" => $post->Store,
                    "Status" => $post->Status,
                    "AddedBy" => $post->AddedBy,
                    "UpdatedBy" => $post->UpdatedBy,
                    "Created_at" => $post->created_at,
                    "Updated_at" => $post->updated_at
                ];
            }

            Redis::set($cacheKey, json_encode($arrayDataRows));

                    return response()->json([
                        'Status' => 200,
                        'TotalRecord' => $posts->count('id'),
                        'DataList' => $arrayDataRows
                    ]);
                } else {
                    return response()->json([
                        "Status" => 0,
                        "TotalRecord" => $posts->count('id'),
                        "Message" => "No Record Found."
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                "Status" => 500,
                "Message" => "An error occurred: " . $e->getMessage()
            ]);
        }
    }
    ########## Store data #############
    public function store(Request $request)
    {
        try{
                $businessvalidation =array(
                    'ClientName' => 'required|string|unique:products,ClientName',
                    'ProductName' => 'required|string',
                    'Status' => 'required|in:Yes,No',
                    'AddedBy' => 'nullable|integer'
                );

                $validatordata = validator::make($request->all(), $businessvalidation);

                if($validatordata->fails()){
                    return response()->json([
                        'status' => '0',
                        'errors' => $validatordata->errors()
                    ], 400);
                }else{
                 $savedata = ProductMaster::create([
                    'ClientName' => $request->ClientName,
                    'ProductName' => $request->ProductName,
                    'ProductPrice' => $request->ProductPrice,
                    'Store' => $request->Store,
                    'Status' => $request->Status,
                    'AddedBy' => $request->AddedBy,
                    'created_at' => now(),
                ]);

                if ($savedata) {
                    Redis::flushall();
                    return response()->json(['Status' => 1, 'Message' => 'Data added successfully!']);
                } else {
                    return response()->json(['Status' => 0, 'Message' =>'Failed to add data.'], 500);
                }
              }
        }catch (\Exception $e){
            return response()->json(['Status' => -1, 'Message' => 'Exception Error Found']);
        }
    }
    ########## Update Data #############
    public function update(Request $request)
    {
        try{
                $businessvalidation =array(
                    'ClientName' => 'required|string',
                    'ProductName' => 'required|string',
                    'Status' => 'required|in:Yes,No',
                    'UpdatedBy' => 'nullable|integer'
                );

                $validatordata = validator::make($request->all(), $businessvalidation);

                if($validatordata->fails()){
                    return response()->json([
                        'status' => '0',
                        'errors' => $validatordata->errors()
                    ], 400);
                }else{

                    // Find existing record
                    $product = ProductMaster::find($request->id);

                    if (!$product) {
                        return response()->json(['Status' => 0, 'Message' => 'Record not found.'], 404);
                    }
                    // Update fields
                    $product->ClientName = $request->ClientName;
                    $product->ProductName = $request->ProductName;
                    $product->ProductPrice = $request->ProductPrice;
                    $product->Store = $request->Store;
                    $product->Status = $request->Status;
                    $product->UpdatedBy = $request->UpdatedBy;
                    $product->updated_at = now();

                    $product->save();
                    Redis::flushall();

                    return response()->json(['Status' => 1, 'Message' => 'Data updated successfully!']);
                    
              }
        }catch (\Exception $e){
            return response()->json(['Status' => -1, 'Message' => 'Exception Error Found']);
        }
    }
    ########## Delete data #############
    public function destroy(Request $request)
    {

        $brands = ProductMaster::find($request->id);
        if (!$brands) {
            return response()->json(['Status' => 0,'result' => 'Data not found.'], 404); // Return 404 if not found
        }
        $brands->delete();
        Redis::flushall();

        if ($brands) {
            return response()->json(['Status' => 1,'result' =>'Data deleted successfully!']);
        } else {
            return response()->json(['Status' => 0,'result' =>'Failed to delete data.'], 500);
        }
    }
}
