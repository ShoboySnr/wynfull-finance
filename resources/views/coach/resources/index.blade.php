@extends('layouts.app')
@section('title', 'Coach Dashboard')

@section('content')
    <!-- Resources Page -->
    <div class="" id="resources">
        <div class="page-content">
            <div class="page-header">
                <h1>Resources</h1>
                <p>Manage educational materials and templates for your clients</p>
            </div>

            <div class="resources-controls">
                <div class="resource-tabs">
                    <button class="resource-tab active" data-filter="all">All Resources</button>
                    <button class="resource-tab" data-filter="templates">Templates</button>
                    <button class="resource-tab" data-filter="word">Word</button>
                    <button class="resource-tab" data-filter="pdf">PDF</button>
                    <button class="resource-tab" data-filter="excel">Excel</button>
                    <button class="resource-tab" data-filter="videos">Videos</button>
                </div>
                <button class="btn-primary">
                    <i class="fas fa-upload"></i>
                    Upload Resource
                </button>
            </div>

            <div class="resources-grid">
                <!-- Templates -->
                <div class="resource-item" data-type="templates">
                    <div class="resource-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Budget Tracker Template</h3>
                        <p>Comprehensive Excel template for monthly budgeting</p>
                        <div class="resource-meta">
                            <span class="resource-type">Excel Template</span>
                            <span class="resource-usage">Used by 18 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="templates">
                    <div class="resource-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Debt Payoff Calculator</h3>
                        <p>Excel template for debt elimination planning</p>
                        <div class="resource-meta">
                            <span class="resource-type">Excel Template</span>
                            <span class="resource-usage">Used by 22 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="templates">
                    <div class="resource-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Financial Goals Worksheet</h3>
                        <p>Template for setting and tracking SMART financial goals</p>
                        <div class="resource-meta">
                            <span class="resource-type">Word Template</span>
                            <span class="resource-usage">Used by 16 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <!-- Word Documents -->
                <div class="resource-item" data-type="word">
                    <div class="resource-icon">
                        <i class="fas fa-file-word"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Investment Strategy Guide</h3>
                        <p>Comprehensive guide to investment planning and strategies</p>
                        <div class="resource-meta">
                            <span class="resource-type">Word Document</span>
                            <span class="resource-usage">Used by 14 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="word">
                    <div class="resource-icon">
                        <i class="fas fa-file-word"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Retirement Planning Workbook</h3>
                        <p>Step-by-step workbook for retirement preparation</p>
                        <div class="resource-meta">
                            <span class="resource-type">Word Document</span>
                            <span class="resource-usage">Used by 19 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <!-- PDF Documents -->
                <div class="resource-item" data-type="pdf">
                    <div class="resource-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Debt Elimination Guide</h3>
                        <p>Step-by-step guide for paying off debt efficiently</p>
                        <div class="resource-meta">
                            <span class="resource-type">PDF Guide</span>
                            <span class="resource-usage">Used by 12 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="pdf">
                    <div class="resource-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Emergency Fund Essentials</h3>
                        <p>Complete guide to building and maintaining emergency funds</p>
                        <div class="resource-meta">
                            <span class="resource-type">PDF Guide</span>
                            <span class="resource-usage">Used by 25 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="pdf">
                    <div class="resource-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Tax Planning Checklist</h3>
                        <p>Annual tax planning and optimization strategies</p>
                        <div class="resource-meta">
                            <span class="resource-type">PDF Checklist</span>
                            <span class="resource-usage">Used by 11 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <!-- Excel Documents -->
                <div class="resource-item" data-type="excel">
                    <div class="resource-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Investment Growth Calculator</h3>
                        <p>Calculate compound growth and retirement projections</p>
                        <div class="resource-meta">
                            <span class="resource-type">Excel Calculator</span>
                            <span class="resource-usage">Used by 24 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="excel">
                    <div class="resource-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Portfolio Tracker</h3>
                        <p>Track investment performance and asset allocation</p>
                        <div class="resource-meta">
                            <span class="resource-type">Excel Spreadsheet</span>
                            <span class="resource-usage">Used by 17 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="excel">
                    <div class="resource-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Cash Flow Analyzer</h3>
                        <p>Analyze monthly cash flow and identify optimization opportunities</p>
                        <div class="resource-meta">
                            <span class="resource-type">Excel Tool</span>
                            <span class="resource-usage">Used by 13 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <!-- Videos -->
                <div class="resource-item" data-type="videos">
                    <div class="resource-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Emergency Fund Basics</h3>
                        <p>Video tutorial on building emergency funds</p>
                        <div class="resource-meta">
                            <span class="resource-type">Video Tutorial</span>
                            <span class="resource-usage">Used by 15 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="videos">
                    <div class="resource-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Budget Creation Masterclass</h3>
                        <p>Complete video course on creating effective budgets</p>
                        <div class="resource-meta">
                            <span class="resource-type">Video Course</span>
                            <span class="resource-usage">Used by 28 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="resource-item" data-type="videos">
                    <div class="resource-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="resource-info">
                        <h3>Investment Fundamentals</h3>
                        <p>Introduction to investing concepts and strategies</p>
                        <div class="resource-meta">
                            <span class="resource-type">Video Series</span>
                            <span class="resource-usage">Used by 21 clients</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary">Share</button>
                        <button class="btn-secondary">Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
@endpush
