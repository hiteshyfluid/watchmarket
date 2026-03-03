@extends('layouts.admin')
@section('title', 'Create Membership Level')

@section('content')
<div class="max-w-5xl">
    <div class="mb-6">
        <a href="{{ route('admin.membership-levels.index') }}" class="text-sm text-gray-500 hover:text-gray-800">&larr; Back to Membership Levels</a>
    </div>

    <form method="POST" action="{{ route('admin.membership-levels.store') }}" class="space-y-6">
        @csrf
        @include('admin.memberships.levels._form')
    </form>
</div>
@endsection

