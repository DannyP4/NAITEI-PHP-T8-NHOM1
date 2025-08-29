<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="API Endpoints của Orders"
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Lấy danh sách tất cả orders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Số trang",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Số lượng orders mỗi trang",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="order_id", type="integer"),
     *                         @OA\Property(property="customer_id", type="integer"),
     *                         @OA\Property(property="order_date", type="string"),
     *                         @OA\Property(property="total_cost", type="number"),
     *                         @OA\Property(property="status", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        
        $orders = Order::with(['user', 'orderItems.product', 'deliveryInfo'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Tạo order mới",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="total_cost", type="number", example=150000),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="order_items", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="integer"),
     *                     @OA\Property(property="price", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="delivery_info", type="object",
     *                 @OA\Property(property="receiver_name", type="string"),
     *                 @OA\Property(property="receiver_phone", type="string"),
     *                 @OA\Property(property="delivery_address", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo order thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order được tạo thành công"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'total_cost' => 'required|numeric|min:0',
            'status' => 'required|string',
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:products,product_id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'delivery_info.receiver_name' => 'required|string|max:255',
            'delivery_info.receiver_phone' => 'required|string|max:15',
            'delivery_info.delivery_address' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'order_date' => now(),
                'total_cost' => $request->total_cost,
                'status' => $request->status
            ]);

            // Tạo order items
            foreach ($request->order_items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            // Tạo delivery info nếu có
            if ($request->has('delivery_info')) {
                $order->deliveryInfo()->create($request->delivery_info);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order được tạo thành công',
                'data' => $order->load(['orderItems.product', 'deliveryInfo'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi tạo order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Lấy thông tin chi tiết của một order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy order",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order không tồn tại")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'orderItems.product', 'deliveryInfo', 'statusOrders.admin'])
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order không tồn tại'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Cập nhật thông tin order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="processing"),
     *             @OA\Property(property="total_cost", type="number", example=200000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order được cập nhật thành công"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order không tồn tại'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|string',
            'total_cost' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update($request->only(['status', 'total_cost']));

        return response()->json([
            'status' => true,
            'message' => 'Order được cập nhật thành công',
            'data' => $order->fresh(['user', 'orderItems.product', 'deliveryInfo'])
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Xóa order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Xóa thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order được xóa thành công")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy order"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order không tồn tại'
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            // Xóa order items trước
            $order->orderItems()->delete();
            
            // Xóa delivery info
            $order->deliveryInfo()->delete();
            
            // Xóa status orders
            $order->statusOrders()->delete();
            
            // Xóa order
            $order->delete();
            
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order được xóa thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi xóa order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/customer/{customerId}",
     *     summary="Lấy danh sách orders theo customer",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công"
     *     )
     * )
     */
    public function getOrdersByCustomer(int $customerId): JsonResponse
    {
        $orders = Order::with(['orderItems.product', 'deliveryInfo'])
            ->where('customer_id', $customerId)
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
}
