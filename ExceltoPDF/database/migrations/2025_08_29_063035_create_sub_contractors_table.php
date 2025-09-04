<?php
// database/migrations/xxxx_xx_xx_create_sub_contractors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_contractors', function (Blueprint $table) {
            $table->id('sub_contractor_id');

            // First, create contractor_id column
            $table->unsignedBigInteger('contractor_id');

            // Then add the foreign key
            $table->foreign('contractor_id')
                  ->references('contractor_id')
                  ->on('contractors')
                  ->onDelete('cascade');

            $table->string('unique_id', 10)->unique();

            $table->string('forename')->nullable();
            $table->string('surname')->nullable();
            $table->string('utr')->nullable();
            $table->string('verification_number')->nullable();

            // Financials
            $table->decimal('total_payment', 10, 2)->nullable();
            $table->decimal('cost_of_materials', 10, 2)->nullable();
            $table->decimal('total_deducted', 10, 2)->nullable();
            $table->decimal('deduction_liability', 10, 2);

            // Generated column
            $table->decimal('amount_payable', 10, 2)
                  ->storedAs('total_payment - total_deducted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_contractors');
    }
};
