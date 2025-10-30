<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Permissao extends Model
    {
        use HasFactory;

        protected $table = 'permissoes';

        protected $fillable = ['nome', 'descricao'];

        public function tiposUtilizadores()
        {
            return $this->belongsToMany(TipoUtilizador::class, 'tipo_utilizador_permissoes');
        }
    }
