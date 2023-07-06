<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('exercise_sets', function (Blueprint $table) {
      $table->id();
      $table->string('file_path');
      $table->string('hash');
      $table->timestamps();
    });

    Schema::create('exercises', function (Blueprint $table) {
      $table->id();
      $table->string('task', 2048);
      $table->string('solution', 2048);
      $table->foreignId('exercise_set_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });

    Schema::create('assignment_groups', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('description');
      $table->foreignId('teacher_id')->constrained('users');
      $table->timestamp('start_date')->nullable();
      $table->timestamp('end_date')->nullable();
      $table->float('max_points', 8, 3);
      $table->timestamps();
    });

    Schema::create('assignments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('assignment_group_id')->constrained();
      $table->foreignId('exercise_set_id')->constrained();
      $table->float('max_points', 8, 3);
      $table->timestamps();
    });

    Schema::create('submissions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('assignment_id')->constrained();
      $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('exercise_id')->nullable()->constrained();
      $table->float('points', 8, 3)->nullable();
      $table->string('provided_solution', 2048)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('submissions');
    Schema::dropIfExists('assignments');
    Schema::dropIfExists('assignment_groups');
    Schema::dropIfExists('exercises');
    Schema::dropIfExists('exercise_sets');
  }
};
