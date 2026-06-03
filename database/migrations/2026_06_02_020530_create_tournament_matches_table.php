<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->integer('round');
            $table->integer('match_number');
            $table->string('bracket')->default('winners'); // winners, final, third_place
            $table->foreignId('participant1_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('participant2_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('winner_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->integer('score1')->nullable();
            $table->integer('score2')->nullable();
            $table->string('status')->default('pending'); // pending, ongoing, finished
            $table->unsignedBigInteger('next_match_id')->nullable(); // for winner advance
            $table->boolean('next_match_slot')->nullable(); // true=slot1, false=slot2
            $table->boolean('is_bye')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
