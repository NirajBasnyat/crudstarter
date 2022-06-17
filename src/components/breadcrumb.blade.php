<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{isset($siteSetting)? $siteSetting->site_name : 'Dashboard'}}</h5>
                </div>
                <ul class="breadcrumb">
                    @isset($parentPageUrl)
                        <li class="breadcrumb-item"><a href="{{$parentPageUrl}}">{{$parentPageTitle}}</a></li>
                    @endisset
                    <li class="breadcrumb-item">{{$currentPageTitle}}</li>
                </ul>
            </div>
        </div>
    </div>
</div>