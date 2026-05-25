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
        Schema::create('medical_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_assistant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->jsonb('parameter_values')->nullable()->comment('Зашифрованные показатели');
            $table->string('pdf_path')->nullable()->comment('Путь к файлу в защищённом хранилище');
            $table->integer('download_count')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('order_item_id');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_results');
    }
};
