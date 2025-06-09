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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('publisher')->nullable();
            $table->string('post_type')->nullable(); // e.g., general
            $table->string('privacy')->nullable(); // e.g., public/private
            $table->json('tagged_user_ids')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable(); // e.g., active
            $table->string('report_status')->nullable();
            $table->json('user_reacts')->nullable();
            $table->string('shared_user')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('posted_on')->nullable();
            $table->string('hashtag')->nullable();
            $table->unsignedBigInteger('album_image_id')->nullable();
            $table->string('mobile_app_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
