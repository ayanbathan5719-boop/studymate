/* Create Post Styles */
.create-post-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
}

.create-post-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

.card-header h2 {
    font-size: 1.8rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.card-header p {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
}

.create-post-form {
    padding: 40px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: #1e293b;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-group label i {
    color: #f59e0b;
}

.required {
    color: #ef4444;
    margin-left: 4px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.form-control:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

textarea.form-control {
    resize: vertical;
    min-height: 200px;
    line-height: 1.6;
}

.help-text {
    display: block;
    margin-top: 6px;
    color: #64748b;
    font-size: 0.85rem;
}

.error-summary {
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 20px;
    margin: 20px 40px;
    color: #b91c1c;
}

.error-summary strong {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.error-summary ul {
    margin-left: 25px;
}

.error-message {
    color: #ef4444;
    font-size: 0.85rem;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.btn {
    padding: 14px 30px;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

/* Select2 Customization */
.select2-container--default .select2-selection--single {
    height: 48px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 12px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px;
    color: #1e293b;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px;
    right: 12px;
}

.select2-dropdown {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.select2-results__option {
    padding: 10px 12px;
}

.select2-results__option--highlighted {
    background: #667eea !important;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .create-post-form {
        padding: 20px;
    }
    
    .error-summary {
        margin: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}