<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contractor_id');
            $table->tinyInteger('processed')->default(0)->comment('0 = not generated, 1 = generated');
            $table->timestamps();

            $table->foreign('contractor_id')
                  ->references('contractor_id')
                  ->on('contractors')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_queue');
    }
};
