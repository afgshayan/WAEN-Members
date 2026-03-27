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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('province', 100)->nullable();
            $table->string('email', 191)->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->string('education', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for fast sorting and filtering
            $table->index('name');
            $table->index('last_name');
            $table->index('province');
            // email is already indexed via unique constraint
            $table->index('education');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
