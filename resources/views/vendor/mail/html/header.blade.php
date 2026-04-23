@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset('assets/img/logo.png') }}" class="logo" alt="CCS Logo">
@if (trim($slot) === 'Laravel')
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
