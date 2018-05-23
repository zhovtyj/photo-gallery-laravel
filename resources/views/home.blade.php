@extends('layouts.app')

@section('content')
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <router-view></router-view>

@endsection
