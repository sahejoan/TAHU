<aside id="sidebar-wrapper">
    <div class="sidebar-brand ">
        <img class="navbar-brand-full app-header-logo" src="{{ asset('img/logo-left.png') }}" width="250"  alt="I-LLANOS">
        <a href="{{ url('/') }}"></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/') }}" class="small-sidebar-text">
            <img class="navbar-brand-full" src="{{ asset('img/logo-left.png') }}" width="80" alt="I-LLANOS"/>
        </a>
    </div>
    <ul class="sidebar-menu">
        @include('layouts.menu')
    </ul>
</aside>
