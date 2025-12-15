@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">CSV Import Instructions</h4>
                </div>
                <div class="card-body">
                    <h5>CSV File Structure:</h5>
                    
                    <div class="alert alert-info">
                        <p><strong>Option 1: Single CSV with all data</strong></p>
                        <p>Include both hierarchy and activities in one CSV file. The system will automatically detect the row type.</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <p><strong>Option 2: Separate CSV files</strong></p>
                        <p>Create two separate CSV files - one for hierarchy, one for activities.</p>
                    </div>
                    
                    <h6 class="mt-4">Hierarchy Rows (Columns):</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Column</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Component Code</td><td>Unique code for component</td><td>Yes</td></tr>
                            <tr><td>Component</td><td>Component name</td><td>Yes</td></tr>
                            <tr><td>Program Code</td><td>Unique code for program</td><td>No</td></tr>
                            <tr><td>Program</td><td>Program name</td><td>No</td></tr>
                            <tr><td>Unit Code</td><td>Unique code for unit</td><td>No</td></tr>
                            <tr><td>Unit</td><td>Unit name</td><td>No</td></tr>
                            <tr><td>Action Code</td><td>Unique code for action</td><td>No</td></tr>
                            <tr><td>Action</td><td>Action name</td><td>No</td></tr>
                            <tr><td>Action Objective</td><td>Action description</td><td>No</td></tr>
                            <tr><td>Action Targets</td><td>Target beneficiaries</td><td>No</td></tr>
                        </tbody>
                    </table>
                    
                    <h6 class="mt-4">Activity Rows (Columns):</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Column</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Action Reference</td><td>Reference like AD.A.3.i.1</td><td>Yes</td></tr>
                            <tr><td>Activity Code</td><td>Unique code for activity</td><td>Yes</td></tr>
                            <tr><td>Activity</td><td>Activity name</td><td>Yes</td></tr>
                            <tr><td>Activity Indicators</td><td>List of indicators (1. indicator 1, 2. indicator 2)</td><td>No</td></tr>
                            <tr><td>Focal Point(s)</td><td>Names separated by comma</td><td>No</td></tr>
                            <tr><td>Status</td><td>ongoing, pending, done, completed</td><td>No</td></tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        <a href="{{ route('reporting.import.download-template') }}" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <a href="{{ route('reporting.import.create') }}" class="btn btn-success">
                            <i class="fas fa-upload"></i> Go to Import
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection