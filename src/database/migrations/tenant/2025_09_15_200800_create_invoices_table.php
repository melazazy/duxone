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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])
                  ->default('draft')
                  ->index();
                  
            $table->decimal('subtotal', 15, 2);
            $table->decimal('vat_amount', 15, 2);
            $table->decimal('total', 15, 2);
            
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('invoice_date');
            $table->index(['client_id', 'invoice_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
