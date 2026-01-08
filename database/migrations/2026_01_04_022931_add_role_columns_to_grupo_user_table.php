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
        Schema::table('grupo_user', function (Blueprint $table) {
            if (!Schema::hasColumn('grupo_user', 'role')) {
                $table->string('role')->default('member');
            }
            if (!Schema::hasColumn('grupo_user', 'added_by')) {
                $table->unsignedBigInteger('added_by')->nullable();
                $table->foreign('added_by')->references('id')->on('users');
            }
            if (!Schema::hasColumn('grupo_user', 'joined_at')) {
                $table->timestamp('joined_at')->useCurrent();
            }
            if (!Schema::hasColumn('grupo_user', 'left_at')) {
                $table->timestamp('left_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupo_user', function (Blueprint $table) {
            $table->dropColumn(['role', 'added_by', 'joined_at', 'left_at']);
        });
    }
};