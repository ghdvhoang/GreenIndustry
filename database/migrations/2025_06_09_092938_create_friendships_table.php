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
         Schema::create('friendships', function (Blueprint $table) {
            $table->increments('id'); // int(11) NOT NULL AUTO_INCREMENT

            $table->unsignedInteger('requester')->nullable();
            $table->unsignedInteger('accepter')->nullable();

            $table->unsignedInteger('importance')->nullable();
            $table->unsignedInteger('is_accepted')->nullable();

            $table->string('accepted_at', 100)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
