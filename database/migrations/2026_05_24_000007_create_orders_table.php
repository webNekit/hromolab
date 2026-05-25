<?php

declare(strict_types=1);

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
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('laboratory_id')->constrained()->restrictOnDelete();
            $table->string('order_number')->unique()->comment('Уникальный номер заказа');
            $table->dateTime('appointment_datetime')->comment('Дата и время записи');
            $table->decimal('total_price', 12, 2);
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->enum('current_status', [
                'new',
                'processing',
                'ready_for_lab',
                'analyzing',
                'completed',
                'cancelled',
            ])->default('new');
            $table->string('payment_id')->nullable()->comment('ID транзакции платёжного шлюза');
            $table->string('promo_code')->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('order_number');
            $table->index('appointment_datetime');
            $table->index('current_status');
            $table->index('payment_status');
            $table->index(['user_id', 'current_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
