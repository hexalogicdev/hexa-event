@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
    $configData = Helper::appClasses();
@endphp
@extends('layouts/commonMaster')
@php
    /* Display elements */
    $contentNavbar = $contentNavbar ?? true;
    $containerNav = $containerNav ?? 'container-fluid';
    $isNavbar = $isNavbar ?? true;
    $isMenu = ($isMenu ?? true) && !(auth()->check() && auth()->user()->isSecurityOfficer());
    $isFlex = $isFlex ?? false;
    $isFooter = $isFooter ?? true;
    $customizerHidden = $customizerHidden ?? '';

    /* HTML Classes */
    $navbarDetached = 'navbar-detached';
    $menuFixed = isset($configData['menuFixed']) ? $configData['menuFixed'] : '';
    if (isset($navbarType)) {
        $configData['navbarType'] = $navbarType;
    }
    $navbarType = isset($configData['navbarType']) ? $configData['navbarType'] : '';
    $footerFixed = isset($configData['footerFixed']) ? $configData['footerFixed'] : '';
    $menuCollapsed = isset($configData['menuCollapsed']) ? $configData['menuCollapsed'] : '';
    /* Content classes */
    $container = 'container-fluid';

@endphp

@section('layoutContent')
    <a class="ep-skip-link" href="#admin-main-content">Skip to main content</a>
    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
        <div class="layout-container">
            @if ($isMenu)
                @include('layouts/sections/menu/verticalMenu')
            @endif
            <div class="layout-page">

                @if ($isNavbar)
                    @include('layouts/sections/navbar/navbar')
                @endif
                <div class="content-wrapper">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <main id="admin-main-content" class="{{ $container }} flex-grow-1 container-p-y" tabindex="-1">
                    @endif
                    @yield('content')

                @if ($isFlex)</div>@else</main>@endif
                @if ($isFooter)
                    @include('layouts/sections/footer/footer')
                @endif
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>

    @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div>
    @endif
    <div class="drag-target"></div>
    </div>
    <script>
        window.successMessage = @json(session('success'));
        window.errorMessage = @json(session('error'));
        window.validationErrors = @json($errors->all());

        document.addEventListener('DOMContentLoaded', () => {
            const menu = document.getElementById('layout-menu');
            const openButton = document.querySelector('.ems-navbar-toggle');
            const closeButton = document.querySelector('.ep-menu-close');
            if (!menu || !openButton) return;

            const expanded = () => document.documentElement.classList.contains('layout-menu-expanded') || document.body.classList.contains('layout-menu-expanded');
            const syncMenuState = () => {
                const isOpen = expanded();
                openButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                menu.setAttribute('aria-hidden', window.innerWidth < 1200 && !isOpen ? 'true' : 'false');
            };

            openButton.setAttribute('aria-expanded', 'false');
            openButton.addEventListener('click', () => window.setTimeout(() => {
                syncMenuState();
                if (expanded()) menu.querySelector('a,button')?.focus();
            }, 0));
            closeButton?.addEventListener('click', () => window.setTimeout(() => {
                syncMenuState();
                openButton.focus();
            }, 0));
            document.addEventListener('keydown', event => {
                if (event.key !== 'Escape' || !expanded() || window.innerWidth >= 1200) return;
                closeButton?.click();
            });
            window.addEventListener('resize', syncMenuState);
            syncMenuState();
        });
    </script>
@endsection
