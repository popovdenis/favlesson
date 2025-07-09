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
        Schema::create('lesson_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');

            $table->date('date');
            $table->unsignedTinyInteger('slot_order');

            $table->time('start_time');
            $table->time('end_time');

            $table->string('comment')->nullable();
            $table->timestamps();

            $table->unique(['term_id', 'group_id', 'date', 'slot_order'], 'unique_slot_per_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons_slot');
    }
};
