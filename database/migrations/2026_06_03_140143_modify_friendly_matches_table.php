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
        Schema::table('friendly_matches', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropColumn(['player_id', 'opponent_name', 'score_home', 'score_away']);
            $table->text('notes')->nullable()->after('match_date');
            $table->integer('total_score_home')->default(0)->after('notes');
            $table->integer('total_score_away')->default(0)->after('total_score_home');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('friendly_matches', function (Blueprint $table) {
            //
        });
    }
};
