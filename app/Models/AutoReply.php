<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoReply extends Model
{
    protected $fillable = [
        'trigger',
        'response',
        'active',
        'priority',
    ];
    protected $casts = [
        'active' => 'boolean',
        'priority' => 'integer',
    ];

    public function setTriggerAttribute($value): void
    {
        $this->attributes['trigger'] = self::normalizeTrigger((string) $value);
    }

    private static function normalizeTrigger(string $trigger): string
    {
        $trigger = trim($trigger);

        // Se já começa por @sistema (qualquer case), normaliza o prefixo para "@sistema"
        if (preg_match('/^@sistema\b/i', $trigger)) {
            // Troca só o prefixo (mantém o resto como o utilizador escreveu)
            $trigger = preg_replace('/^@sistema\b/i', '@sistema', $trigger);

            // Garante espaço a seguir ao prefixo quando há conteúdo extra
            if ($trigger !== '@sistema' && !preg_match('/^@sistema\s+/', $trigger)) {
                $trigger = preg_replace('/^@sistema\b/', '@sistema ', $trigger);
            }

            return $trigger;
        }

        // Se não começar por @sistema, prefixa automaticamente
        return $trigger === '' ? '@sistema' : '@sistema ' . $trigger;
    }
}