<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->enum('role', ['admin', 'student', 'teacher'])->default('student');
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->unique();
      $table->string('password');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};