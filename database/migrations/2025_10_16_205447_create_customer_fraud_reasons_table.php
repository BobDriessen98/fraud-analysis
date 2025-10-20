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
        Schema::create('customer_fraud_reasons', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('fraud_reason_id');
            $table->string('context');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_fraud_reasons');
    }
};
