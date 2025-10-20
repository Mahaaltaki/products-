<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->decimal('total_amount', 10, 2); //  بدون الخسم إجمالي مبلغ الطلب
            $table->string('status')->default('pending');
            $table->timestamps();



            // فهرس على total_amount
            $table->index('total_amount');

            // فهرس على created_at
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
         // لحذف الفهارس بعد ما حذفنا الجدول
         $table->dropIndex(['total_amount']); 

         $table->dropIndex(['created_at']); 
    }
};
