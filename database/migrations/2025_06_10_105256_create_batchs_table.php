<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batchs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->string('icon', 255)->nullable();
            $table->integer('status')->nullable();
            $table->timestamp('start_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('end_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batchs');
    }
};
