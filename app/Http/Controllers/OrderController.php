<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Order;
use App\OrderItem;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    /**
     * @OA\Get(
     *      path="/orders",
     *      tags={"Orders"},
     *      summary="Obtener todas las órdenes",
     *      description="Regresa la información perteneciente a
     *      todas las órdenes hechas por un usuario en particular",
     *      @OA\Response(
     *          response=200,
     *          description="Operacion exitosa"
     *       ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     *
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
     * @OA\Post(
     *     path="/orders",
     *     summary="Crear una orden",
     *     description="Añade la informacion perteneciente a una orden",
     *      tags={"Orders"},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *       @OA\Response(
     *         response=404,
     *         description="El paquete, como order item, no existe"
     *     ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */
    public function store(CreateOrderRequest $request)
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
        return response()->json(new OrderResource($order), 201);
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
