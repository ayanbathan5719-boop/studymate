<style>
/* ===== MAIN CONTAINER ===== */
.resource-show-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
    background: #f9fafb;
    min-height: 100vh;
}

/* ===== GRID LAYOUT ===== */
.resource-show-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    margin-top: 20px;
}

@media (max-width: 1024px) {
    .resource-show-grid {
        grid-template-columns: 1fr;
    }
    
    .resource-sidebar {
        position: static !important;
        margin-top: 0;
    }
}

/* ===== ALERT MESSAGES ===== */
.alert-success, .alert-error {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    animation: slideDown 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.alert-success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-left: 4px solid #22c55e;
    color: #166534;
}

.alert-error {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-left: 4px solid #ef4444;
    color: #991b1b;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== CARDS STYLING ===== */
.resource-header-card,
.resource-content-card,
.study-notes-card,
.related-resources-card,
.sidebar-card {
    background: white;
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.resource-header-card:hover,
.resource-content-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* ===== RESOURCE HEADER ===== */
.resource-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 40px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 20px;
    background: #f1f5f9;
    color: #1e293b;
}

.resource-type-badge.pdf { background: #fee2e2; color: #dc2626; }
.resource-type-badge.video { background: #dbeafe; color: #2563eb; }
.resource-type-badge.link { background: #d1fae5; color: #059669; }

.resource-title {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 20px;
    line-height: 1.3;
}

/* ===== RESOURCE META ===== */
.resource-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    padding: 20px 0;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 24px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 12px;
    font-size: 0.875rem;
    color: #334155;
}

.meta-item i {
    width: 20px;
    color: #6366f1;
}

.meta-item strong {
    color: #0f172a;
}

/* ===== RESOURCE DESCRIPTION ===== */
.resource-description {
    margin-top: 24px;
}

.resource-description h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.description-content {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    line-height: 1.6;
    color: #334155;
}

/* ===== RESOURCE PREVIEW ===== */
.resource-content-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.resource-preview {
    background: #f8fafc;
    border-radius: 16px;
    padding: 24px;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pdf-preview embed,
.pdf-preview iframe {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.video-preview iframe,
.video-preview video {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* ===== ACTION BUTTONS ===== */
.resource-actions {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    margin-top: 28px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.btn-action {
    padding: 12px 28px;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    border: none;
}

.btn-download {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(99,102,241,0.3);
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99,102,241,0.4);
    color: white;
}

.btn-studied {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(16,185,129,0.3);
}

.btn-studied:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.4);
    color: white;
}

.btn-external {
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
    color: white;
}

.btn-external:hover {
    transform: translateY(-2px);
    color: white;
}

/* ===== STUDY NOTES ===== */
.study-notes-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.notes-form textarea {
    width: 100%;
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    font-family: inherit;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    resize: vertical;
    background: #f8fafc;
}

.notes-form textarea:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}

.btn-save-notes {
    margin-top: 16px;
    padding: 10px 24px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 40px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-save-notes:hover {
    background: #6366f1;
    color: white;
    border-color: #6366f1;
}

/* ===== RELATED RESOURCES ===== */
.related-resources-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.related-item {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.related-item:hover {
    transform: translateX(4px);
    background: #f1f5f9;
    text-decoration: none;
}

.related-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
}

.related-icon.pdf { background: #fee2e2; color: #dc2626; }
.related-icon.video { background: #dbeafe; color: #2563eb; }
.related-icon.link { background: #d1fae5; color: #059669; }

.related-info h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 6px;
}

.related-meta {
    font-size: 0.75rem;
    color: #64748b;
}

/* ===== NAVIGATION FOOTER ===== */
.navigation-footer {
    margin-top: 24px;
    text-align: left;
}

.btn-back-to-topic {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 40px;
    color: #475569;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-back-to-topic:hover {
    background: #f8fafc;
    transform: translateX(-4px);
    text-decoration: none;
    color: #6366f1;
}

/* ===== SIDEBAR ===== */
.resource-sidebar {
    position: sticky;
    top: 24px;
    height: fit-content;
}

.sidebar-card {
    margin-bottom: 24px;
}

.sidebar-card h4 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #0f172a;
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
}

.unit-code-large {
    font-size: 1.5rem;
    font-weight: 700;
    color: #6366f1;
    margin-bottom: 8px;
}

.unit-name {
    font-weight: 500;
    color: #334155;
    margin-bottom: 12px;
}

.course-name {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 16px;
}

.btn-view-unit,
.btn-view-topic {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
    color: #6366f1;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Progress Bar */
.topic-progress {
    margin: 16px 0;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    margin-bottom: 6px;
    color: #64748b;
}

.progress-bar {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Activity Stats */
.activity-stat {
    text-align: center;
    padding: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    color: white;
    margin-bottom: 16px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 0.75rem;
    opacity: 0.9;
}

.last-downloaded,
.marked-studied {
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    font-size: 0.75rem;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Tags Cloud */
.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-badge {
    padding: 6px 12px;
    background: #f1f5f9;
    border-radius: 20px;
    font-size: 0.75rem;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s ease;
}

.tag-badge:hover {
    background: #6366f1;
    color: white;
    text-decoration: none;
}

/* ===== RESPONSIVE ADJUSTMENTS ===== */
@media (max-width: 768px) {
    .resource-show-container {
        padding: 16px;
    }
    
    .resource-header-card,
    .resource-content-card,
    .study-notes-card,
    .related-resources-card,
    .sidebar-card {
        padding: 20px;
    }
    
    .resource-title {
        font-size: 1.5rem;
    }
    
    .resource-meta {
        grid-template-columns: 1fr;
    }
    
    .resource-actions {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
    }
}

/* ===== PRINT STYLES ===== */
@media print {
    .resource-actions,
    .navigation-footer,
    .resource-sidebar,
    .btn-save-notes {
        display: none;
    }
    
    .resource-show-container {
        padding: 0;
    }
    
    .resource-header-card,
    .resource-content-card {
        box-shadow: none;
        padding: 0;
        margin-bottom: 20px;
    }
}
</style>