<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\Grupo;

return new class extends Migration
{
    public function up(): void
    {
        Grupo::with('criador')->get()->each(function ($grupo) {
            if ($grupo->criador) {
                $grupo->membros()->syncWithoutDetaching([$grupo->criador_id => ['is_admin' => 1]]);
            }
        });
    }

    public function down(): void
    {
        // No reversal needed
    }
};