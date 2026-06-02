<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stored_resumes', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('user_id');
            $table->string('original_filename')->nullable()->after('file_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stored_resumes', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'original_filename']);
        });
    }
};
