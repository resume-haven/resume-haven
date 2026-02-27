<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('analysis_cache', function (Blueprint $table) {
            $table->string('request_hash', 64)->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('analysis_cache', function (Blueprint $table) {
            $table->dropColumn('request_hash');
        });
    }
};
