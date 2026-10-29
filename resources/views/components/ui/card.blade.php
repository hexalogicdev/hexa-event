@props(['title' => null, 'description' => null, 'bodyClass' => 'ep-card__body'])
<section {{ $attributes->class(['ep-card']) }}>@if($title || $description || isset($actions))<header class="ep-card__header"><div>@if($title)<h2>{{ $title }}</h2>@endif @if($description)<p>{{ $description }}</p>@endif</div>@isset($actions)<div>{{ $actions }}</div>@endisset</header>@endif<div class="{{ $bodyClass }}">{{ $slot }}</div></section>

