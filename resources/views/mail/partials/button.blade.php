@props([
  'url' => '#',
  'label' => 'Open',
  'bg' => '#0E4DA4',    // Wynfull blue
  'color' => '#FFFFFF', // text color
])

<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="left" style="margin:16px 0;">
    <tr>
        <td align="center" bgcolor="{{ $bg }}" style="border-radius:8px;">
            <a href="{{ $url }}" target="_blank"
               style="display:inline-block; padding:12px 18px; color:{{ $color }}; text-decoration:none; font-size:14px; font-weight:bold; border-radius:8px;">
                {{ $label }}
            </a>
        </td>
    </tr>
</table>
