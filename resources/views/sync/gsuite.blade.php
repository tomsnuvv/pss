@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<div class="bg-white shadow rounded-lg p-8 max-w-login mx-auto">
    @component('nova::auth.partials.heading')
        {{ __('GSuite Integration') }}
    @endcomponent

    <div class="flex px-8 py-4">
        <a target="_blank" class="w-full btn btn-default btn-primary hover:bg-primary-dark" href="{{ route('sync.gsuite.auth') }}">
            {{ __('Request an auth code') }}
        </a>
    </div>

    @if(!empty($message))
        <div class="alert alert-success"> {{ $message }}</div>
    @endif

    <form method="post" action="{{ route('sync.gsuite.store') }}">
        @csrf
        <div class="flex py-4">
            <input type="text" name="code" placeholder="Auth code" class="w-full form-control form-input form-input-bordered">
        </div>
        <div class="flex px-8 py-4">
            <button class="w-full btn btn-default btn-danger hover:bg-danger-dark" type="submit">Save</button>
        </div>
        <div class="flex px-8 py-4">
            <a class="w-full btn btn-default btn-primary hover:bg-primary-dark" href="{{ route('sync.gsuite.sync') }}">
                Sync
            </a>
        </div>
    </form>
</div>
@endsection
