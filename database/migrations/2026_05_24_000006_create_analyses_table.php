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
        Schema::create('analyses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->comment('Артикул');
            $table->text('description')->nullable();
            $table->text('preparation')->nullable()->comment('Правила подготовки');
            $table->string('biomaterial')->comment('Тип биоматериала');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('lead_time_days')->comment('Срок выполнения в днях');
            $table->json('reference_ranges')->nullable()->comment('Референсные значения JSON');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
            $table->index('sku');
            $table->index('is_active');
            $table->index('is_popular');
        });

        // Full-text search index via raw SQL for PostgreSQL
        // For SQLite (dev), we skip FULLTEXT as it's not supported
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE analyses ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('russian', coalesce(name, '')), 'A') ||
                setweight(to_tsvector('russian', coalesce(description, '')), 'B') ||
                setweight(to_tsvector('russian', coalesce(sku, '')), 'C')
            ) STORED");
            DB::statement('CREATE INDEX analyses_search_idx ON analyses USING gin(search_vector)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
