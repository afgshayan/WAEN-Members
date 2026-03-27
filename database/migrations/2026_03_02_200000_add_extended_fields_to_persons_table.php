<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('city', 100)->nullable()->after('province');
            $table->string('country', 100)->nullable()->after('city');
            $table->string('whatsapp', 30)->nullable()->after('phone');
            $table->string('gender', 20)->nullable()->after('education');
            $table->smallInteger('birth_year')->unsigned()->nullable()->after('gender');
            $table->string('event_name', 150)->nullable()->after('birth_year');
            $table->text('notes')->nullable()->after('event_name');

            $table->index('city');
            $table->index('country');
            $table->index('event_name');
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['country']);
            $table->dropIndex(['event_name']);
            $table->dropColumn(['city', 'country', 'whatsapp', 'gender', 'birth_year', 'event_name', 'notes']);
        });
    }
};
