@props([
    'url',
    'color' => 'primary',
])

<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 18px 0;">
<tr>
<td align="center">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
    <td align="center">
        <table border="0" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
        <td align="center">
            <a href="{{ $url }}"
               class="button button-{{ $color }}"
               target="_blank"
               rel="noopener"
               style="border-radius:999px; font-weight:800; letter-spacing:.2px;">
                {{ $slot }}
            </a>
        </td>
        </tr>
        </table>
    </td>
    </tr>
    </table>
</td>
</tr>
</table>
