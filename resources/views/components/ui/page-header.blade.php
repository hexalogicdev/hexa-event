@props(['title', 'description' => null, 'eyebrow' => null])
<header {{ $attributes->class(['ep-page-header']) }}><div class="ep-page-header__copy">@if($eyebrow)<span class="ep-page-header__eyebrow">{{ $eyebrow }}</span>@endif<h1 class="ep-page-header__title">{{ $title }}</h1>@if($description)<p class="ep-page-header__description">{{ $description }}</p>@endif</div>@isset($actions)<div class="ep-page-header__actions">{{ $actions }}</div>@endisset</header>

