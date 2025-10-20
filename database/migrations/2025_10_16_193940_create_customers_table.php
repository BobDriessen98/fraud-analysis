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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('bsn')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('tag')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_postcode')->nullable();
            $table->string('address_city')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('iban')->nullable();
            $table->date('last_invoice_date')->nullable();
            $table->dateTime('last_login_date_time')->nullable();
            $table->timestamps();

            /*
             * For this assessment I am using SQLite, therefore constraints/FK relations on database level
             * are not possible. If it were possible I would create a FK relation to the scans table.
             */

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
