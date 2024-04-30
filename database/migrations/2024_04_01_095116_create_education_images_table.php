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
        Schema::create('education_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('education_id');
            $table->string('url');
            $table->tinyInteger('is_info');
            $table->timestamps();
            $table->foreign('education_id')
                ->references('id')
                ->on('education')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_images');
    }
};
