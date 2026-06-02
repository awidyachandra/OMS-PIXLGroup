<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quality_controls', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('unit_id');

            $table->boolean('on')->default(false);
            $table->boolean('off')->default(false);
            $table->boolean('clear')->default(false);

            $table->string('result');
            $table->date('qc_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_controls');
    }
};