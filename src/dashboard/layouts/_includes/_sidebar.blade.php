<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{url('home')}}" class="app-brand-link">
            <span class="app-brand-text demo text-body fw-bolder text-uppercase">LOGO</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <x-sidebar-item route="{{route('home')}}" name="Dashboard" uri="home">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
        </x-sidebar-item>


        {{--    Example of multisidebar    --}}
        {{--<x-sidebar-multilist-head icon="bx bxs-report" name="Reports" :routes="[
            'admin/package/reports',
            'admin/package/filters',
           ]">

            <x-sidebar-item route="{{route('admin.package-reports.filters')}}" name="Package Filter" uri="admin/package/filters">
            </x-sidebar-item>
            <x-sidebar-item route="{{route('admin.package-reports.index')}}" name="Package Report" uri="admin/package/reports">
            </x-sidebar-item>

        </x-sidebar-multilist-head>--}}
    </ul>
</aside>
