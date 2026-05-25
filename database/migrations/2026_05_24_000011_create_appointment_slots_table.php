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
        Schema::create('appointment_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('laboratory_id')->constrained()->cascadeOnDelete();
            $table->dateTime('slot_datetime')->comment('Дата и время слота');
            $table->unsignedTinyInteger('capacity')->default(1)->comment('Макс. количество записей на слот');
            $table->unsignedTinyInteger('booked')->default(0)->comment('Количество записей');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['laboratory_id', 'slot_datetime']);
            $table->index(['laboratory_id', 'slot_datetime', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};
