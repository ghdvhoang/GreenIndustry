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
        Schema::create('media_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('post_id')->nullable();
            $table->integer('story_id')->nullable();
            $table->integer('album_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('page_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('chat_id')->nullable();
            $table->integer('album_image_id')->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('file_type', 255)->nullable();
            $table->string('privacy', 200)->nullable();
            $table->string('created_at', 100)->nullable(); // nếu bạn dùng timestamp thực, nên dùng $table->timestamps()
            $table->string('updated_at', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
