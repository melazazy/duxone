<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 🔹 العلاقة مع الشركة (Multi-Tenancy)
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade');

            // 🔹 البيانات الأساسية للعنصر
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->string('unit', 50)->default('unit');

            // 🔹 معلومات إضافية
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->enum('type', ['product', 'service'])->default('service');
            $table->enum('status', ['active', 'inactive'])->default('active');

            // 🔹 نظام التتبع
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
