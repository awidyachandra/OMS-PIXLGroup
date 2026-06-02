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
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            $table->string('event')->nullable();
            $table->string('package');
            $table->date('date');

            $table->decimal('total_price', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable();
            $table->decimal('final_price', 12, 2)->nullable();

            $table->enum('status', [
                'pending approval',
                'dp paid',
                'fully paid',
                'completed'
            ])->default('pending approval');

            $table->timestamps();
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
