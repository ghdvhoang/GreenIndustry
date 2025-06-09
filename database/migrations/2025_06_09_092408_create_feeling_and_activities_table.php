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
        Schema::create('feeling_and_activities', function (Blueprint $table) {
            $table->increments('feeling_and_activity_id'); // PRIMARY KEY
            $table->string('type', 255);
            $table->string('title', 255);
            $table->string('icon', 255);
            $table->string('created_at', 100); // Vì gốc dùng varchar
            $table->string('updated_at', 100); // Vì gốc dùng varchar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeling_and_activities');
    }
};
