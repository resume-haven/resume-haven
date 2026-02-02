<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function __construct(private readonly \Illuminate\Database\Schema\Builder $builder)
    {
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->builder->create('cache', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        $this->builder->create('cache_locks', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->builder->dropIfExists('cache');
        $this->builder->dropIfExists('cache_locks');
    }
};
