@props([
  'url' => '#',
  'label' => 'Open',
  'bg' => '#0E4DA4',    // Wynfull blue
  'color' => '#FFFFFF', // text color
  'fullWidth' => true,  // Make full width by default
])

<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:16px 0;">
    <tr>
        <td align="center" bgcolor="{{ $bg }}" style="border-radius:8px; {{ $fullWidth ? 'width:100%;' : '' }}">
            <a href="{{ $url }}" target="_blank"
               style="display:{{ $fullWidth ? 'block' : 'inline-block' }}; width:{{ $fullWidth ? '100%' : 'auto' }}; padding:16px 24px; color:{{ $color }}; text-decoration:none; font-size:16px; font-weight:600; border-radius:8px; text-align:center; box-sizing:border-box;">
                {{ $label }}
            </a>
        </td>
    </tr>
</table>
