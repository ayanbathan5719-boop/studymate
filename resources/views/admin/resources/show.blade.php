@extends('admin.layouts.master')

@section('title', 'Resource Details')
@section('page-icon', 'fa-file-alt')
@section('page-title', 'Resource Details')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Resources
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>{{ $resource->title }}</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                 <tr>
                    <th width="150">Type</th>
                    <td><span class="badge bg-info">{{ ucfirst($resource->file_type) }}</span></td>
                </tr>
                <tr>
                    <th>Unit</th>
                    <td>{{ $resource->unit_code }}</td>
                </tr>
                <tr>
                    <th>Uploaded By</th>
                    <td>{{ $resource->user->name ?? 'Unknown' }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $resource->description ?? 'No description' }}</td>
                </tr>
                <tr>
                    <th>Views</th>
                    <td>{{ number_format($resource->views_count ?? 0) }}</td>
                </tr>
                <tr>
                    <th>Downloads</th>
                    <td>{{ number_format($resource->download_count ?? 0) }}</td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td>{{ $resource->created_at->format('F d, Y H:i') }}</td>
                </tr>
            </table>

            @if($resource->file_type == 'link')
                <div class="alert alert-info">
                    <i class="fas fa-link"></i> External Link: 
                    <a href="{{ $resource->url }}" target="_blank">{{ $resource->url }}</a>
                </div>
                <a href="{{ $resource->url }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Open Link
                </a>
            @else
                <a href="{{ route('admin.resources.download', $resource) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Download File
                </a>
            @endif
        </div>
    </div>
</div>
@endsection