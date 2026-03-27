<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename', 255);
            $table->string('original_name', 255);
            $table->string('path', 500);
            $table->string('disk', 50)->default('public');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('type', 20)->default('file'); // image, document, file
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('mime_type');
            $table->index('uploaded_by');
            $table->index('created_at');
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
