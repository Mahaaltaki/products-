
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Apply a discount to a specific order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyOrderDiscount(Request $request, Order $order)
    {
        
        try {
            $request->validate([
                'discount_type' => ['required', 'string', 'in:percentage,fixed'],
                'discount_value' => ['required', 'numeric', 'min:0.01'],
                'discount_code' => ['nullable', 'string', 'max:50']
            ]);

            $discountData = [
                'type' => $request->input('discount_type'),
                'value' => $request->input('discount_value'),
                'code' => $request->input('discount_code'), 
            ];

            $finalTotal = $this->discountService->applyDiscount($order, $discountData);

            
            return response()->json([
                'message' => 'Discount applied successfully',
                'original_total' => $order->total_amount,
                'final_total' => $finalTotal,
                'discount_applied' => $order->total_amount - $finalTotal
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400); // Bad Request
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}