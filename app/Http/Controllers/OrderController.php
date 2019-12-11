<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderItem;
use App\Package;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $total = 0;
        $orderItems = $request->get("order_items");
        $order = new Order();

        foreach($orderItems as $orderItem) {
            $package = Package::find($orderItem['package_id']);

            if(!$package) {
                return response()->json([
                    "error" => "Some Package doesn't exists"
                ]);
            }
            $total += $package->price;

            $orderItem = new OrderItem();

            $orderItem->orders()->associate($order);
            $orderItem->packages()->associate($package);
            $orderItem->save();
        }

        $order->total = $total;
        $order->card_id = "0";
        $order->user()->save(auth('api')->user());

        $order->save();

        return response()->json($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
