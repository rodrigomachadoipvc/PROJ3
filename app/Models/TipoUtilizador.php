<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class TipoUtilizador extends Model
    {
        use HasFactory;
        
        protected $table = 'tipos_utilizadores';
        
        protected $fillable = ['nome', 'descricao'];

        public function utilizadores()
        {
            return $this->hasMany(User::class);
        }


        public function permissoes()
        {
            return $this->belongsToMany(Permissao::class, 'tipo_utilizador_permissoes');
        }
    }
