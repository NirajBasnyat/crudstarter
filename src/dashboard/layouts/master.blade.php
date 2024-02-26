@include('layouts._includes._links')

@guest
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                @yield("content")
            </div>
        </div>
    </div>
@else

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            @include('layouts._includes._sidebar')

            <div class="layout-page">

                @include('layouts._includes._navbar')


                <div class="content-wrapper">
                    <div id="app" class="flex-grow-1 container-p-y">
                        @yield("content")
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
@endguest

@include('layouts._includes._scripts')
