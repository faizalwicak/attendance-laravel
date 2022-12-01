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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('name', 100);
            $table->string('email', 100)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->string('image', 100)->nullable();
            $table->string('role', 10);
            $table->unsignedBigInteger('school_id')->nullable();
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->foreign('grade_id')
                ->references('id')
                ->on('grades')
                ->onDelete('cascade');
            $table->string('gender', 6)->nullable(); // MALE / FEMALE
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
