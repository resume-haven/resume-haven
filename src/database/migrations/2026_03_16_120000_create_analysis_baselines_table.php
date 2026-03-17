<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('analysis_baselines', function (Blueprint $table): void {
            $table->id();
            $table->string('baseline_key', 191);
            $table->string('job_hash', 64);
            $table->unsignedTinyInteger('score_percentage');
            $table->unsignedInteger('match_count');
            $table->unsignedInteger('gap_count');
            $table->json('recommendations')->nullable();
            $table->timestamps();

            $table->unique(['baseline_key', 'job_hash']);
            $table->index('baseline_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_baselines');
    }
};
