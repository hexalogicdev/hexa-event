@props(['label', 'value', 'meta' => null, 'icon' => 'chart-bar'])
<article {{ $attributes->class(['ep-stat']) }}><span class="ep-stat__icon" aria-hidden="true"><i class="ti ti-{{ $icon }}"></i></span><div class="min-w-0"><span class="ep-stat__label">{{ $label }}</span><strong class="ep-stat__value">{{ $value }}</strong>@if($meta)<span class="ep-stat__meta">{{ $meta }}</span>@endif</div></article>

