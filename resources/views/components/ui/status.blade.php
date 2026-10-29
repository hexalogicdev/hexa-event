@props(['tone' => 'neutral'])
<span {{ $attributes->class(['ep-status', 'ep-status--'.$tone]) }}>{{ $slot }}</span>

