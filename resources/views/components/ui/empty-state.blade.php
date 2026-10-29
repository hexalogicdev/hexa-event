@props(['title', 'description' => null, 'icon' => 'inbox'])
<div {{ $attributes->class(['ep-empty']) }}><i class="ti ti-{{ $icon }} ep-empty__icon" aria-hidden="true"></i><strong class="ep-empty__title">{{ $title }}</strong>@if($description)<span class="ep-empty__description">{{ $description }}</span>@endif{{ $slot }}</div>

