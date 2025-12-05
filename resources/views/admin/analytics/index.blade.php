@extends('layouts.app')

@section('title', 'Advanced Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Advanced Analytics
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Comprehensive insights and statistics for your content management system.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="mb-6 flex items-center gap-4 flex-wrap">
        <label class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            Time Period:
        </label>
        <select id="periodSelector" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif;">
            <option value="7">Last 7 days</option>
            <option value="30" selected>Last 30 days</option>
            <option value="90">Last 90 days</option>
            <option value="180">Last 6 months</option>
            <option value="365">Last year</option>
        </select>
        <button onclick="refreshAnalytics()" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            Refresh Data
        </button>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-accent"></div>
        <p class="mt-4 text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            Loading analytics data...
        </p>
    </div>

    <!-- Overview Statistics -->
    <div id="overviewStats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats will be loaded dynamically -->
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Views Over Time -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Views Over Time
            </h2>
            <canvas id="viewsOverTimeChart" height="300"></canvas>
        </div>

        <!-- Content Growth -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Content Growth
            </h2>
            <canvas id="contentGrowthChart" height="300"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Content by Type -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Content by Type
            </h2>
            <canvas id="contentByTypeChart" height="300"></canvas>
        </div>

        <!-- Views by Type -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Views by Content Type
            </h2>
            <canvas id="viewsByTypeChart" height="300"></canvas>
        </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Status Breakdown -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Status Breakdown
            </h2>
            <canvas id="statusBreakdownChart" height="250"></canvas>
        </div>

        <!-- Source Breakdown -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Content Source
            </h2>
            <canvas id="sourceBreakdownChart" height="250"></canvas>
        </div>

        <!-- Daily Activity -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Daily Activity
            </h2>
            <canvas id="dailyActivityChart" height="250"></canvas>
        </div>
    </div>

    <!-- Top Content & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Viewed Content -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Top Viewed Content
                </h2>
                <select id="topContentType" class="px-3 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" onchange="loadTopContent()">
                    <option value="all">All</option>
                    <option value="movies">Movies</option>
                    <option value="tv_shows">TV Shows</option>
                </select>
            </div>
            <div id="topContentList" class="space-y-3">
                <!-- Top content will be loaded here -->
            </div>
        </div>

        <!-- Episode Analytics -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Episode Analytics
            </h2>
            <div id="episodeStats" class="space-y-4">
                <!-- Episode stats will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Comments Analytics -->
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Comments Analytics
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div id="commentsStats" class="space-y-2">
                <!-- Comments stats will be loaded here -->
            </div>
        </div>
        <div>
            <canvas id="commentsOverTimeChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let charts = {};
let currentPeriod = 30;

// Initialize analytics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAllAnalytics();
    
    // Period selector change handler
    document.getElementById('periodSelector').addEventListener('change', function() {
        currentPeriod = this.value;
        loadAllAnalytics();
    });
});

function refreshAnalytics() {
    loadAllAnalytics();
}

function showLoading() {
    document.getElementById('loadingIndicator').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingIndicator').classList.add('hidden');
}

async function loadAllAnalytics() {
    showLoading();
    
    try {
        await Promise.all([
            loadOverviewStats(),
            loadViewsOverTime(),
            loadContentGrowth(),
            loadContentByType(),
            loadViewsByType(),
            loadStatusBreakdown(),
            loadSourceBreakdown(),
            loadDailyActivity(),
            loadTopContent(),
            loadEpisodeAnalytics(),
            loadCommentsAnalytics()
        ]);
    } catch (error) {
        console.error('Error loading analytics:', error);
    } finally {
        hideLoading();
    }
}

// Load overview statistics
async function loadOverviewStats() {
    try {
        const response = await fetch(`{{ route('admin.analytics.overview') }}?period=${currentPeriod}`);
        const data = await response.json();
        
        const statsHtml = `
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider mb-2">Total Content</p>
                <p class="text-3xl font-bold text-gray-900 dark:!text-white">${data.total_content.toLocaleString()}</p>
                <p class="text-xs text-gray-500 dark:!text-text-secondary mt-1">+${data.content_added_period} this period</p>
            </div>
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider mb-2">Total Views</p>
                <p class="text-3xl font-bold text-gray-900 dark:!text-white">${data.total_views.toLocaleString()}</p>
                <p class="text-xs text-gray-500 dark:!text-text-secondary mt-1">+${data.views_period.toLocaleString()} this period</p>
            </div>
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider mb-2">Total Episodes</p>
                <p class="text-3xl font-bold text-gray-900 dark:!text-white">${data.total_episodes.toLocaleString()}</p>
                <p class="text-xs text-gray-500 dark:!text-text-secondary mt-1">+${data.episodes_added_period} this period</p>
            </div>
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider mb-2">Total Comments</p>
                <p class="text-3xl font-bold text-gray-900 dark:!text-white">${data.total_comments.toLocaleString()}</p>
                <p class="text-xs text-gray-500 dark:!text-text-secondary mt-1">+${data.comments_period} this period</p>
            </div>
        `;
        
        document.getElementById('overviewStats').innerHTML = statsHtml;
    } catch (error) {
        console.error('Error loading overview stats:', error);
    }
}

// Load views over time chart
async function loadViewsOverTime() {
    try {
        const response = await fetch(`{{ route('admin.analytics.views-over-time') }}?period=${currentPeriod}`);
        const data = await response.json();
        
        const ctx = document.getElementById('viewsOverTimeChart').getContext('2d');
        
        if (charts.viewsOverTime) {
            charts.viewsOverTime.destroy();
        }
        
        charts.viewsOverTime = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Views',
                    data: data.views,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading views over time:', error);
    }
}

// Load content growth chart
async function loadContentGrowth() {
    try {
        const response = await fetch(`{{ route('admin.analytics.content-growth') }}?period=${currentPeriod}`);
        const data = await response.json();
        
        const ctx = document.getElementById('contentGrowthChart').getContext('2d');
        
        if (charts.contentGrowth) {
            charts.contentGrowth.destroy();
        }
        
        charts.contentGrowth = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Movies',
                    data: data.movies,
                    backgroundColor: '#3B82F6'
                }, {
                    label: 'TV Shows',
                    data: data.tv_shows,
                    backgroundColor: '#10B981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading content growth:', error);
    }
}

// Load content by type chart
async function loadContentByType() {
    try {
        const response = await fetch(`{{ route('admin.analytics.content-by-type') }}`);
        const data = await response.json();
        
        const ctx = document.getElementById('contentByTypeChart').getContext('2d');
        
        if (charts.contentByType) {
            charts.contentByType.destroy();
        }
        
        charts.contentByType = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } catch (error) {
        console.error('Error loading content by type:', error);
    }
}

// Load views by type chart
async function loadViewsByType() {
    try {
        const response = await fetch(`{{ route('admin.analytics.views-by-type') }}`);
        const data = await response.json();
        
        const ctx = document.getElementById('viewsByTypeChart').getContext('2d');
        
        if (charts.viewsByType) {
            charts.viewsByType.destroy();
        }
        
        charts.viewsByType = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Views',
                    data: data.views,
                    backgroundColor: '#F59E0B'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading views by type:', error);
    }
}

// Load status breakdown chart
async function loadStatusBreakdown() {
    try {
        const response = await fetch(`{{ route('admin.analytics.status-breakdown') }}`);
        const data = await response.json();
        
        const ctx = document.getElementById('statusBreakdownChart').getContext('2d');
        
        if (charts.statusBreakdown) {
            charts.statusBreakdown.destroy();
        }
        
        charts.statusBreakdown = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: ['#10B981', '#6B7280', '#F59E0B']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } catch (error) {
        console.error('Error loading status breakdown:', error);
    }
}

// Load source breakdown chart
async function loadSourceBreakdown() {
    try {
        const response = await fetch(`{{ route('admin.analytics.source-breakdown') }}`);
        const data = await response.json();
        
        const ctx = document.getElementById('sourceBreakdownChart').getContext('2d');
        
        if (charts.sourceBreakdown) {
            charts.sourceBreakdown.destroy();
        }
        
        charts.sourceBreakdown = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: ['#3B82F6', '#10B981']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } catch (error) {
        console.error('Error loading source breakdown:', error);
    }
}

// Load daily activity chart
async function loadDailyActivity() {
    try {
        const response = await fetch(`{{ route('admin.analytics.daily-stats') }}?days=${currentPeriod}`);
        const stats = await response.json();
        
        const labels = stats.map(s => s.label);
        const contentAdded = stats.map(s => s.content_added);
        const episodesAdded = stats.map(s => s.episodes_added);
        const comments = stats.map(s => s.comments);
        
        const ctx = document.getElementById('dailyActivityChart').getContext('2d');
        
        if (charts.dailyActivity) {
            charts.dailyActivity.destroy();
        }
        
        charts.dailyActivity = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Content',
                    data: contentAdded,
                    borderColor: '#3B82F6',
                    tension: 0.4
                }, {
                    label: 'Episodes',
                    data: episodesAdded,
                    borderColor: '#10B981',
                    tension: 0.4
                }, {
                    label: 'Comments',
                    data: comments,
                    borderColor: '#F59E0B',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading daily activity:', error);
    }
}

// Load top content
async function loadTopContent() {
    try {
        const type = document.getElementById('topContentType')?.value || 'all';
        const response = await fetch(`{{ route('admin.analytics.top-content') }}?type=${type}&metric=views&limit=10`);
        const content = await response.json();
        
        let html = '';
        content.forEach((item, index) => {
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:!bg-bg-card-hover rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-gray-400">#${index + 1}</span>
                        <div>
                            <a href="${item.url}" class="font-semibold text-gray-900 dark:!text-white hover:text-accent">${item.title}</a>
                            <p class="text-xs text-gray-500 dark:!text-text-secondary">${item.type.replace('_', ' ')}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 dark:!text-white">${item.views.toLocaleString()}</p>
                        <p class="text-xs text-gray-500 dark:!text-text-secondary">views</p>
                    </div>
                </div>
            `;
        });
        
        document.getElementById('topContentList').innerHTML = html || '<p class="text-gray-500">No content found</p>';
    } catch (error) {
        console.error('Error loading top content:', error);
    }
}

// Load episode analytics
async function loadEpisodeAnalytics() {
    try {
        const response = await fetch(`{{ route('admin.analytics.episodes') }}?period=${currentPeriod}`);
        const data = await response.json();
        
        const html = `
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:!bg-bg-card-hover rounded-lg">
                    <span class="text-gray-700 dark:!text-white font-semibold">Total Episodes</span>
                    <span class="text-xl font-bold text-gray-900 dark:!text-white">${data.total_episodes.toLocaleString()}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:!bg-bg-card-hover rounded-lg">
                    <span class="text-gray-700 dark:!text-white font-semibold">Published</span>
                    <span class="text-xl font-bold text-green-600">${data.published_episodes.toLocaleString()}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:!bg-bg-card-hover rounded-lg">
                    <span class="text-gray-700 dark:!text-white font-semibold">Added This Period</span>
                    <span class="text-xl font-bold text-blue-600">${data.episodes_added_period.toLocaleString()}</span>
                </div>
            </div>
        `;
        
        document.getElementById('episodeStats').innerHTML = html;
    } catch (error) {
        console.error('Error loading episode analytics:', error);
    }
}

// Load comments analytics
async function loadCommentsAnalytics() {
    try {
        const response = await fetch(`{{ route('admin.analytics.comments') }}?period=${currentPeriod}`);
        const data = await response.json();
        
        const statsHtml = `
            <div class="bg-green-50 dark:!bg-green-900/20 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:!text-text-secondary">Approved</p>
                <p class="text-2xl font-bold text-green-600 dark:!text-green-400">${data.approved_comments.toLocaleString()}</p>
            </div>
            <div class="bg-yellow-50 dark:!bg-yellow-900/20 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:!text-text-secondary">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 dark:!text-yellow-400">${data.pending_comments.toLocaleString()}</p>
            </div>
            <div class="bg-red-50 dark:!bg-red-900/20 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:!text-text-secondary">Rejected</p>
                <p class="text-2xl font-bold text-red-600 dark:!text-red-400">${data.rejected_comments.toLocaleString()}</p>
            </div>
            <div class="bg-gray-50 dark:!bg-gray-800 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:!text-text-secondary">This Period</p>
                <p class="text-2xl font-bold text-gray-900 dark:!text-white">${data.comments_period.toLocaleString()}</p>
            </div>
        `;
        
        document.getElementById('commentsStats').innerHTML = statsHtml;
        
        // Load comments over time chart
        if (data.comments_over_time) {
            const ctx = document.getElementById('commentsOverTimeChart').getContext('2d');
            
            if (charts.commentsOverTime) {
                charts.commentsOverTime.destroy();
            }
            
            charts.commentsOverTime = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.comments_over_time.labels,
                    datasets: [{
                        label: 'Comments',
                        data: data.comments_over_time.data,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading comments analytics:', error);
    }
}
</script>

<style>
canvas {
    max-height: 300px;
}
</style>
@endsection
