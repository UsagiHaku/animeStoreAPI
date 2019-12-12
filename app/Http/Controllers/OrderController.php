<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Order;
use App\OrderItem;
use App\Package;
use App\Serie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $myOrders = auth('api')->user()->orders()->with('orderItems.package')->get();

        return response()->json(OrderResource::collection($myOrders));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $total = 0;
        $orderItems = $request->get("order_items");
        $order = new Order();

        foreach ($orderItems as $orderItem) {
            $package = Package::find($orderItem['package_id']);

            if (!$package) {
                return response()->json([
                    "error" => "Some Package doesn't exists"
                ], 404);
            }

            $total += $package->price;
        }

        $order->total = $total;
        $order->card_id = "0";
        $order->delivery_date = now();
        $order->user()->associate(auth('api')->user());

        $order->save();

        foreach ($orderItems as $orderItem) {
            $package = Package::find($orderItem['package_id']);
            $orderItem = new OrderItem();

            $orderItem->order()->associate($order);
            $orderItem->package()->associate($package);
            $orderItem->save();

            foreach ($package->series()->get() as $serie) {
                auth("api")->user()->series()->save($serie);
                auth("api")->user()->save();
            }
        }

        $order->save();
        return response()->json($order);
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show()
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
