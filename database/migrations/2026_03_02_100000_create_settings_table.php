<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 60)->default('general')->index();
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        $defaults = [
            // General
            ['key' => 'app_name',              'value' => 'Nonprofit Members Portal', 'group' => 'general'],
            ['key' => 'app_description',       'value' => 'Member Management System', 'group' => 'general'],
            ['key' => 'timezone',              'value' => 'UTC',          'group' => 'general'],
            ['key' => 'per_page_default',      'value' => '100',          'group' => 'general'],
            ['key' => 'date_format',           'value' => 'Y-m-d',        'group' => 'general'],

            // Security
            ['key' => 'session_lifetime',      'value' => '120',          'group' => 'security'],
            ['key' => 'login_max_attempts',    'value' => '5',            'group' => 'security'],
            ['key' => 'login_decay_seconds',   'value' => '60',           'group' => 'security'],
            ['key' => 'force_https',           'value' => '0',            'group' => 'security'],
            ['key' => 'remember_me_days',      'value' => '30',           'group' => 'security'],

            // CAPTCHA
            ['key' => 'captcha_type',          'value' => 'none',         'group' => 'captcha'],
            ['key' => 'captcha_site_key',      'value' => '',             'group' => 'captcha'],
            ['key' => 'captcha_secret_key',    'value' => '',             'group' => 'captcha'],
            ['key' => 'captcha_theme',         'value' => 'light',        'group' => 'captcha'],
            ['key' => 'captcha_language',      'value' => 'en',           'group' => 'captcha'],

            // Import / Export
            ['key' => 'import_batch_size',     'value' => '500',          'group' => 'import_export'],
            ['key' => 'export_chunk_size',     'value' => '1000',         'group' => 'import_export'],
            ['key' => 'max_upload_mb',         'value' => '50',           'group' => 'import_export'],

            // Root access page
            ['key' => 'root_access_title',   'value' => 'Access Restricted',                    'group' => 'general'],
            ['key' => 'root_access_message', 'value' => 'You do not have permission to access this area. Please use the application link provided by your administrator.', 'group' => 'general'],
        ];

        foreach ($defaults as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('settings')->insertOrIgnore($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
