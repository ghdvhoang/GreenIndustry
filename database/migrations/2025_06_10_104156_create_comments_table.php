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
        Schema::create('comments', function (Blueprint $table) {
        $table->id('comment_id');
        $table->unsignedBigInteger('parent_id')->default(0);
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('is_type', 100)->nullable()->comment("post, event, any other type post's comment");
        $table->unsignedBigInteger('id_of_type')->nullable();
        $table->longText('description')->nullable();
        $table->longText('user_reacts')->nullable();
        $table->string('created_at', 100)->nullable();
        $table->string('updated_at', 100)->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
