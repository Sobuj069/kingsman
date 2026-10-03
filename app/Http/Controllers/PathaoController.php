<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PathaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
 {
        $tokenResponse = getPathaoAccessToken();
        dd($tokenResponse); // Debugging
        // $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxMDE2NCIsImp0aSI6IjNiZTk2MzFkOGJiMWU1MDQwNTJkZmJjYWNlNGI2MmRlNmQzZTdkZWI1Nzk5YWNmNzU0NmYwZTYzMWViODcxYzM4OTliODk1ZmQzMTQ1N2I4IiwiaWF0IjoxNzQyMzc0MTk2LjM0NTMzOCwibmJmIjoxNzQyMzc0MTk2LjM0NTM0MSwiZXhwIjoxNzUwMTUwMTk2LjI3NDg2NSwic3ViIjoiMTA0Nzg0Iiwic2NvcGVzIjpbXX0.MyCpn7bOrnL9XDEnyIy_yDo8HnMXXdTXGM7Y6d4_6VgEM3Y6gYJUhNwR0XiYPwlPW8_6HakBa4jouT57RMI4KLxdiGkokV1d4Z6CQ4oIM3wto9pYCnfS1QVJEcwWwH09wtEnx5CrarZzHe9wMs5b6mX-UscM4dd-HmYgCu72iMaLuWRdrcADFirz2fu0eMOJqM1prqVDmDxhXU7_QbtOhV7xdyT0-274rpcM6KwJD5_jugFbLOQndSy--54-ZAo_ki8lJsz5wox1Nb4Sk89Q0R2n5Z7dL5ojXYFrsMqJHXv-FSMeuEbtJK5sSmXjtk9Ls2Yu_iUBk30ktD5J72ZdHBrXd85GDsHwOYK7nY8Vc93wNowflbWvV1OXz15qnRu9LCX7APW29Ok_XujOQxmNYrkqvcDrE6uU27NiBi1AOPOm9U93TVw1gOs8K8av-6r0rpeSstTkh9hgZob-xSm4x41kqMjln8XyV0SH5CAji1_m7Zk-1eekEpnHZSxFKhB3wUq3DLKvxYHzb0z6DFqGF6CvasTi5dYHBwMFXE58J2jYaJnVNGiFm0S7EpzsilWZEMjAEJglpzz-9rsBBtFcdIouzyXDzHh_uUy2gVcoh4kepiQeJ5GcznLLzTITS3LwQAobCypAU3VG7hXOBv1TIGvpoGdyA4M7mveVQDHXDwA';
        // $cityList = getStoreList($accessToken);
        // foreach($cityList['data'] as $shop){
        //     Shop::create([
        //         'store_id' => $shop['store_id'],
        //         'shop_name' => $shop['store_name'],
        //     ]);
        // }
        // $token = env('PATHAO_SECRET_TOKEN');
        // // if($data->shop_id==1){
        // // }else{
        // //     $token = env('PATHAO_SECRET_TOKEN2');
        // // }
        // $id = "DB190325VVEQ6T";

        // $orderSummary = getOrderSummary($id, $token);
        // dd($orderSummary['data']['order_status']);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
 {
        // dd(FacadesPathaoCourier::VIEW_ORDER('DB1302257FECCX'));
        // $data['shops'] = FacadesPathaoCourier::GET_STORES()['data']['data'];
        // $data['citits'] =  FacadesPathaoCourier::GET_CITIES()['data']['data'];
        // dd($data['shops']);
        
        return view('backend.pages.pathao.create');
    }

    
    public function getZonesByCity(Request $request)
    {
        $token = env('PATHAO_SECRET_TOKEN');
        // $response =FacadesPathaoCourier::GET_ZONES($request->city_id);
        $zoneList = getZoneList($request->city_id, $token);
        dd($zoneList);
        $response = $zoneList;

        return response()->json($response);
    }
    public function getAreasByCity(Request $request)
    {
        $token = env('PATHAO_SECRET_TOKEN');
        // Simulated API response based on selected city_id
        // $response =FacadesPathaoCourier::GET_AREAS($request->zone_id);
        $response = getAreaList($request->zone_id, $token);

        return response()->json($response);
    }

    public function store(Request $request)
    {
        // $requestData = new PathaoOrderRequest([
        //     'store_id' => $request->store_id,
        //     'sender_name' => 'ahmad',
        //     'sender_phone' => '01901166585',
        //     'recipient_name' => $request->recipient_name,
        //     'recipient_phone' => $request->recipient_phone,
        //     'recipient_address' => $request->recipient_address,
        //     'recipient_city' => $request->recipient_city,
        //     'recipient_zone' => $request->recipient_zone,
        //     'recipient_area' => $request->recipient_area,
        //     'delivery_type' => $request->delivery_type,
        //     'item_type' => $request->item_type,
        //     'special_instruction' => $request->special_instruction,
        //     'item_quantity' => $request->item_quantity,
        //     'item_weight' => $request->item_weight,
        //     'item_description' => $request->item_description,
        //     'amount_to_collect' => $request->amount_to_collect,
        // ]);
        // $response =FacadesPathaoCourier::CREATE_ORDER($requestData);

        // dd($response);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
