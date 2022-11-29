<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id');
            $table->foreign('record_id')
                ->references('id')
                ->on('records')
                ->onDelete('cascade');
            $table->time('clock_in_time')->nullable();
            $table->time('clock_out_time')->nullable();
            $table->decimal('clock_in_lat', 9, 6)->nullable();
            $table->decimal('clock_in_lng', 9, 6)->nullable();
            $table->decimal('clock_out_lat', 9, 6)->nullable();
            $table->decimal('clock_out_lng', 9, 6)->nullable();
            $table->string('clock_in_status', 10)->nullable(); // LATE / ON_TIME

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attends');
    }
};
