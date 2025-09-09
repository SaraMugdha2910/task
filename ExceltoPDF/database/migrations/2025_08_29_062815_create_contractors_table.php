<?php

// database/migrations/xxxx_xx_xx_create_contractors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->unsignedBigInteger('contractor_id')->autoIncrement()->primary();
            $table->string('contractor_forename')->nullable();
            $table->string('contractor_surname')->nullable();
            $table->string('employer_tax_reference')->nullable();
            $table->string('email')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2');
            $table->string('pincode')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
