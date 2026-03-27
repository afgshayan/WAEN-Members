<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn(['city', 'birth_year']);
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('city', 100)->nullable()->after('province');
            $table->unsignedSmallInteger('birth_year')->nullable()->after('gender');
        });
    }
};
