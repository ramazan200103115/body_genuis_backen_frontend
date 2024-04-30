<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Change the 'age' column to be nullable
            $table->integer('age')->nullable()->change();

            // Change the 'aboutMe' column to be nullable
            $table->text('aboutMe')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert 'age' column to not nullable
            // This assumes 'age' was previously an integer and not nullable
            $table->integer('age')->default(20);

            // Revert 'aboutMe' column to not nullable
            // This assumes 'aboutMe' was previously a text and not nullable
            $table->text('aboutMe')->nullable();
        });
    }
};
