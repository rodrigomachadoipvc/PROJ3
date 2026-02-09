@php
    // Base URL usado para gerar links/imagens nos emails.
    // NOTA: emails enviados a partir de localhost NÃO conseguem servir imagens ao Gmail.
    // Por isso, quando detetamos localhost, fazemos fallback para o domínio público.
    $baseUrl = rtrim(config('app.url'), '/');

    $isLocal = str_contains($baseUrl, 'localhost')
        || str_contains($baseUrl, '127.0.0.1')
        || str_contains($baseUrl, '0.0.0.0');

    if ($isLocal) {
        // Fallback para o teu domínio público (sem mexer no .env)
        $baseUrl = 'https://communico.pt';
    }

    // Usa PNG no email (melhor compatibilidade do que SVG)
    $logoUrl = $baseUrl . '/images/logo_communico_mail.png';
@endphp

<tr>
<td class="header">
<a href="{{ $baseUrl }}" style="display: inline-block; text-decoration: none;">
    <img
        src="{{ $logoUrl }}"
        class="logo"
        alt="{{ config('app.name', 'Communico') }}"
        style="width: 160px; max-width: 160px; height: auto; display: block;"
    >
</a>
</td>
</tr>
