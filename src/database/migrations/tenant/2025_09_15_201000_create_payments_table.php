<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            
            $table->enum('status', ['pending', 'confirmed', 'failed'])
                  ->default('pending')
                  ->index();
                  
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
