<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            // Rename existing columns
            $table->renameColumn('name', 'first_name');
            $table->renameColumn('notes', 'biography');
        });

        Schema::table('persons', function (Blueprint $table) {
            // Add new columns
            $table->date('date_of_birth')->nullable()->after('last_name');
            $table->string('occupation', 200)->nullable()->after('date_of_birth');
            $table->string('waen_email', 191)->nullable()->after('email');
            $table->string('street_address', 255)->nullable()->after('whatsapp');
            $table->string('apartment', 100)->nullable()->after('street_address');
            $table->string('city', 100)->nullable()->after('apartment');
            $table->string('state_province', 100)->nullable()->after('city');
            $table->string('zip_code', 20)->nullable()->after('state_province');
            $table->string('facebook', 255)->nullable()->after('country');
            $table->string('instagram', 255)->nullable()->after('facebook');
            $table->string('linkedin', 255)->nullable()->after('instagram');
            $table->string('twitter', 255)->nullable()->after('linkedin');
            $table->string('headshot', 500)->nullable()->after('biography');
            $table->string('cv_file', 500)->nullable()->after('headshot');
            $table->text('areas_of_expertise')->nullable()->after('cv_file');
            $table->text('proposed_initiatives')->nullable()->after('areas_of_expertise');

            // Drop columns that are no longer needed
            $table->dropIndex('persons_province_index');
            $table->dropIndex('persons_education_index');
            $table->dropIndex('persons_event_name_index');
            $table->dropColumn(['province', 'education', 'gender', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('province', 100)->nullable();
            $table->string('education', 100)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('event_name', 150)->nullable();

            $table->index('province', 'persons_province_index');
            $table->index('education', 'persons_education_index');
            $table->index('event_name', 'persons_event_name_index');

            $table->dropColumn([
                'date_of_birth', 'occupation', 'waen_email',
                'street_address', 'apartment', 'city', 'state_province', 'zip_code',
                'facebook', 'instagram', 'linkedin', 'twitter',
                'headshot', 'cv_file', 'areas_of_expertise', 'proposed_initiatives',
            ]);
        });

        Schema::table('persons', function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->renameColumn('biography', 'notes');
        });
    }
};
