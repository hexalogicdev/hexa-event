@php
    use Illuminate\Support\Facades\Route;
    $currentEnv = env('APP_ENV'); // Get the current environment
@endphp

<ul class="menu-sub">
    @if (isset($menu))
        @foreach ($menu as $submenu)
            {{-- Environment check --}}
            @if (!isset($submenu->env) || in_array($currentEnv, $submenu->env))
                {{-- Render if env matches or is not set --}}

                {{-- active menu method --}}
                @php
                    $activeClass = null;
                    $active = $configData['layout'] === 'vertical' ? 'active open' : 'active';
                    $currentRouteName = Route::currentRouteName();

                    if ($currentRouteName === $submenu->slug) {
                        $activeClass = 'active';
                    } elseif (isset($submenu->submenu)) {
                        if (gettype($submenu->slug) === 'array') {
                            foreach ($submenu->slug as $slug) {
                                if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                                    $activeClass = $active;
                                }
                            }
                        } else {
                            if (
                                str_contains($currentRouteName, $submenu->slug) &&
                                strpos($currentRouteName, $submenu->slug) === 0
                            ) {
                                $activeClass = $active;
                            }
                        }
                    }
                @endphp

                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                        class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>
                        @if (isset($submenu->icon))
                            <i class="{{ $submenu->icon }}"></i>
                        @endif
                        <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
                        @isset($submenu->badge)
                            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}
                            </div>
                        @endisset
                    </a>

                    {{-- Recursive submenu --}}
                    @if (isset($submenu->submenu))
                        @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
                    @endif
                </li>
            @endif {{-- End of environment check --}}
        @endforeach
    @endif
</ul>
