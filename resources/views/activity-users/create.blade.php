{{-- resources/views/activity-users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Activity-User Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Activity-User Assignment</h3>
                    <div class="card-tools">
                        <a href="{{ route('activity-users.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @livewire('activity-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection