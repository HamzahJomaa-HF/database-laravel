{{-- resources/views/activity-users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Activity-User Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Activity-User Assignment</h3>
                    <div class="card-tools">
                        <a href="{{ route('activity-users.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @livewire('activity-user-form', ['id' => $id])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection