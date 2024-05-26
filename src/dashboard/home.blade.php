@extends('layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="row mb-2">
            <div class="col-sm-6 col-lg-3 mb-4">
                <x-dashboard.stat-card icon="bx bx-user" color="dark" name="Users" count="123" link="{{route('home')}}"/>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <x-dashboard.stat-card icon="bx bx-package" color="primary" name="Packages" count="123" />
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <x-dashboard.stat-card icon="bx bxs-plane-alt" color="danger" name="Destinations" count="123" />
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <x-dashboard.stat-card icon="bx bx-walk" color="success" name="Activities" count="123" />
            </div>
        </div>
    </div>
@endsection
