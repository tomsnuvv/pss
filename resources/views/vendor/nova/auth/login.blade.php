@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<div class="bg-white shadow rounded-lg p-8 max-w-login mx-auto">
    @component('nova::auth.partials.heading')
        {{ __('Welcome Back!') }}
    @endcomponent

    <a class="w-full btn btn-default btn-primary hover:bg-primary-dark" href="{{ route('auth.google') }}">
        {{ __('Login using your Google account') }}
    </a>
</div>
@endsection
