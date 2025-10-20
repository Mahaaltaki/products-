<?php

namespace App\Services;

use App\Models\Order; 

class DiscountService
{
    /**
     * Applies a discount to an order and returns the final total.
     *
     * @param Order $order The order instance.
     * @param array $discount An array containing discount details:
     *                        [
     *                            'type' => 'percentage' or 'fixed',
     *                            'value' => the discount amount or percentage,
     *                            'code' => 'Optional discount code for logging/tracking'
     *                        ]
     * @return float The final order total after applying the discount.
     * @throws \InvalidArgumentException If discount type is invalid or value is non-positive.
     */
    public function applyDiscount(Order $order, array $discount): float
    {
        $originalTotal = $order->total_amount;
        $discountedAmount = 0.0;
        $finalTotal = $originalTotal;

    
        if (!isset($discount['type']) || !isset($discount['value'])) {
            throw new \InvalidArgumentException("Discount array must contain 'type' and 'value'.");
        }

        $discountType = strtolower($discount['type']);
        $discountValue = (float) $discount['value'];

        if ($discountValue <= 0) {
            throw new \InvalidArgumentException("Discount value must be positive.");
        }

        //  حساب قيمة الخصم 
        
        if ($discountType === 'percentage') {
        
            if ($discountValue < 0 || $discountValue > 100) {
                throw new \InvalidArgumentException("Percentage discount must be between 0 and 100.");
            }
            $calculatedDiscount = $originalTotal * ($discountValue / 100);
        } elseif ($discountType === 'fixed') {
            $calculatedDiscount = $discountValue;
        } else {
            throw new \InvalidArgumentException("Invalid discount type. Must be 'percentage' or 'fixed'.");
        }

        $maxDiscountAllowed = $originalTotal * 0.50;

        if ($calculatedDiscount > $maxDiscountAllowed) {
            $discountedAmount = $maxDiscountAllowed;
            \Log::info("Discount for Order ID: {$order->id} was capped at 50% of total. Original discount calculated: {$calculatedDiscount}, capped at: {$maxDiscountedAmount}");
        } else {
            $discountedAmount = $calculatedDiscount;
        }

        $finalTotal = max(0, $originalTotal - $discountedAmount);

        
        return (float) $finalTotal;
    }
}