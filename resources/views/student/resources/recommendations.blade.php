@extends('student.layouts.master')

@section('title', 'Recommended Resources')
@section('page-icon', 'fa-star')
@section('page-title', 'Recommended for You')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.resources.index') }}"><i class="fas fa-folder-open"></i> Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recommendations</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="recommendations-container">
    {{-- Header --}}
    <div class="recommendations-header">
        <div class="header-content">
            <h1><i class="fas fa-star"></i> Recommended for You</h1>
            <p class="subtitle">Personalized resources based on your study patterns</p>
        </div>
    </div>

    @if($recommendations->isEmpty())
        {{-- Empty State --}}
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3>No Recommendations Yet</h3>
            <p>Start studying to get personalized resource recommendations.</p>
            <a href="{{ route('student.resources.index') }}" class="btn-browse">
                <i class="fas fa-folder-open"></i> Browse All Resources
            </a>
        </div>
    @else
        {{-- Recommendations Grid --}}
        <div class="recommendations-grid">
            @foreach($recommendations as $resource)
                <div class="recommendation-card">
                    <div class="card-header">
                        <div class="resource-type {{ $resource->type }}">
                            @if($resource->type === 'pdf')
                                <i class="fas fa-file-pdf"></i>
                            @elseif($resource->type === 'video')
                                <i class="fas fa-video"></i>
                            @elseif($resource->type === 'link')
                                <i class="fas fa-link"></i>
                            @else
                                <i class="fas fa-file-alt"></i>
                            @endif
                        </div>
                        <span class="unit-badge">{{ $resource->unit_code }}</span>
                    </div>

                    <div class="card-body">
                        <h3 class="resource-title">
                            <a href="{{ route('student.resources.show', $resource->id) }}">
                                {{ $resource->title }}
                            </a>
                        </h3>

                        @if($resource->description)
                            <p class="resource-description">
                                {{ Str::limit($resource->description, 100) }}
                            </p>
                        @endif

                        <div class="resource-meta">
                            @if($resource->topic)
                                <span class="meta-topic">
                                    <i class="fas fa-tag"></i> {{ $resource->topic->name }}
                                </span>
                            @endif
                            
                            <span class="meta-downloads">
                                <i class="fas fa-download"></i> {{ $resource->downloads_count ?? 0 }}
                            </span>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="recommendation-reason">
                            @if($resource->topic && in_array($resource->topic_id, $strugglingTopics ?? []))
                                <i class="fas fa-chart-line"></i> Based on your study focus
                            @else
                                <i class="fas fa-fire"></i> Popular in your units
                            @endif
                        </div>
                        
                        <a href="{{ route('student.resources.show', $resource->id) }}" class="btn-view">
                            View Resource <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Why These Recommendations --}}
        <div class="why-section">
            <h2><i class="fas fa-question-circle"></i> How recommendations work</h2>
            <div class="why-grid">
                <div class="why-card">
                    <div class="why-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Your Study Time</h3>
                    <p>Resources from topics where you've spent the most time are prioritized.</p>
                </div>
                
                <div class="why-card">
                    <div class="why-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Completion Rate</h3>
                    <p>We suggest resources to help you complete topics you've started.</p>
                </div>
                
                <div class="why-card">
                    <div class="why-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h3>Popular Resources</h3>
                    <p>Highly downloaded resources from your units are recommended.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.recommendations-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 24px;
}

.breadcrumb-item a {
    color: #64748b;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #f59e0b;
}

.breadcrumb-item.active {
    color: #0f172a;
    font-weight: 500;
}

.recommendations-header {
    margin-bottom: 32px;
}

.header-content h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.header-content h1 i {
    color: #f59e0b;
    background: #fffbeb;
    padding: 8px;
    border-radius: 14px;
    font-size: 1.5rem;
}

.header-content .subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-left: 52px;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 24px;
    border: 2px dashed #f1f5f9;
}

.empty-icon i {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 24px;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 12px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 24px;
}

.btn-browse {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: #f59e0b;
    color: white;
    text-decoration: none;
    border-radius: 40px;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-browse:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
    margin-bottom: 48px;
}

.recommendation-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    overflow: hidden;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
}

.recommendation-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    border-color: #f59e0b;
}

.card-header {
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.resource-type {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.resource-type.pdf {
    background: #fee2e2;
    color: #dc2626;
}

.resource-type.video {
    background: #dbeafe;
    color: #2563eb;
}

.resource-type.link {
    background: #fef3c7;
    color: #d97706;
}

.resource-type.document {
    background: #e0f2fe;
    color: #0284c7;
}

.unit-badge {
    background: white;
    color: #475569;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.8rem;
    font-weight: 600;
}

.card-body {
    padding: 20px;
    flex: 1;
}

.resource-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.resource-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}

.resource-title a:hover {
    color: #f59e0b;
}

.resource-description {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 16px;
}

.resource-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 0.85rem;
    color: #64748b;
}

.meta-topic,
.meta-downloads {
    display: flex;
    align-items: center;
    gap: 4px;
}

.meta-topic i,
.meta-downloads i {
    color: #f59e0b;
    font-size: 0.8rem;
}

.card-footer {
    padding: 20px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}

.recommendation-reason {
    font-size: 0.85rem;
    color: #f59e0b;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.recommendation-reason i {
    font-size: 0.8rem;
}

.btn-view {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f59e0b;
    color: white;
    text-decoration: none;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
    width: 100%;
    justify-content: center;
}

.btn-view:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.why-section {
    margin-top: 48px;
}

.why-section h2 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.why-section h2 i {
    color: #f59e0b;
}

.why-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.why-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    padding: 24px;
    text-align: center;
    transition: all 0.2s ease;
}

.why-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
}

.why-icon {
    width: 60px;
    height: 60px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.why-icon i {
    font-size: 28px;
    color: white;
}

.why-card h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
}

.why-card p {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0;
}

@media (max-width: 768px) {
    .recommendations-container {
        padding: 16px;
    }
    
    .recommendations-grid {
        grid-template-columns: 1fr;
    }
    
    .why-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush