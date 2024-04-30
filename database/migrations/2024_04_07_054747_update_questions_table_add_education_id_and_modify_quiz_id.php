<?php

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
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('quiz_id')->nullable()->change();
            // Add a new nullable 'education_id' column
            $table->unsignedBigInteger('education_id')->nullable()->after('quiz_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('quiz_id')->change();
            // Remove the 'education_id' column
            $table->dropColumn('education_id');
        });
    }
};
