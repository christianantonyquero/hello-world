<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - {{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/admin/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('css/admin/admin-app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app" class="admin-wrapper">
        <div class="row no-gutters">

            <div id="sidebar" class="col-md-4 col-lg-2 admin-sidebar dark">
                @include('admin.partials._sidebar')
            </div>

            <main class="col-md-8 col-lg-10 admin-content">
                <nav class="navbar navbar-expand sticky-top navbar-light bg-white admin-nav">
                     
                    <!-- Left Side Of Navbar -->
                    <h4 class="navbar-text section-title mr-4">
                        @yield('title')
                    </h4>

                    <div class="btn-toolbar">
                        @yield('header-buttons')
                    </div>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item logout" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <img src="{{ asset('img/admin/icons/logout.svg') }}" />&
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            </div>
                        </li>

                    </ul>
                       
                </nav>

                <div class="content">
                    @include('flash::message')
                    @yield('content')
                </div>

            </main>
        </div>

        @include('admin.partials.add_store')
        @include('admin.partials.edit_store')
        @include('admin.partials.add_user')
        @if(app()->request->store)
            @push('scripts')
                <script>

                    var product_categories = {!! json_encode(app()->request->store->productCategories) !!};
                </script>
            @endpush
        @endif

        @if(app()->request->is('*/users'))
            @include('admin.partials.edit_user')
        @endif

    </div>

    @stack('scripts')

</body>
</html>