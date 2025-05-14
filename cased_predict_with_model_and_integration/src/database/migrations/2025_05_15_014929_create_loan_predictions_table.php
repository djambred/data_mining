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
        Schema::create('loan_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('age');
            $table->decimal('salary', 15, 2);
            $table->decimal('loan_amount', 15, 2);
            $table->date('loan_date');
            $table->date('payday');
            $table->boolean('predicted_kemampuan_bayar')->default(false);; // 1 = mampu bayar, 0 = tidak
            $table->decimal('total_pemberian_pinjaman', 15, 2)->nullable();
            $table->unsignedInteger('lama_pinjaman')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_predictions');
    }
};
