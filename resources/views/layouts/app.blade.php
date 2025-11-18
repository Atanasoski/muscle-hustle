<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Muscle Hustle') }} - @yield('title', 'Fitness Tracker')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Custom Styles -->
    <style>
        :root {
            /* Muscle Hustle Signature Colors */
            --primary-color: #ff6b35;      /* Orange/Coral - Primary actions */
            --secondary-color: #4ecdc4;    /* Teal - Secondary accents */
            --navy-color: #2c3e50;         /* Dark Navy - Headers */
            --success-color: #44bd32;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;
            
            /* Light theme */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --border-color: #dee2e6;
            --card-bg: #ffffff;
            --navbar-bg: #2c3e50;
            --navbar-text: #ffffff;
        }

        [data-theme="dark"] {
            --bg-primary: #1a1a2e;
            --bg-secondary: #16213e;
            --bg-tertiary: #0f3460;
            --text-primary: #eaeaea;
            --text-secondary: #b8b8b8;
            --border-color: #2d3748;
            --card-bg: #16213e;
            --navbar-bg: #0f0f1e;
            --navbar-text: #ffffff;
        }

        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: var(--navbar-bg) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--navbar-text) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .nav-link {
            color: var(--navbar-text) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 107, 53, 0.1);
            color: var(--primary-color) !important;
        }

        /* Theme Toggle */
        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--primary-color);
        }

        .theme-toggle i {
            font-size: 1.2rem;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background: var(--bg-tertiary);
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c61 100%);
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #54d462 100%);
            border: none;
            font-weight: 600;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(68, 189, 50, 0.4);
        }
        
        /* Teal/Secondary Button */
        .btn-info {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #5dd9d1 100%);
            border: none;
            font-weight: 600;
            color: white;
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
            color: white;
        }

        /* Alerts */
        .alert {
            border-radius: 0.75rem;
            border: none;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(68, 189, 50, 0.1) 0%, rgba(84, 212, 98, 0.1) 100%);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1) 0%, rgba(242, 93, 78, 0.1) 100%);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        /* Tables */
        .table {
            color: var(--text-primary);
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: var(--bg-secondary);
        }

        /* Forms */
        .form-control, .form-select {
            background-color: var(--bg-secondary);
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-secondary);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        /* Dropdown */
        .dropdown-menu {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border-radius: 0.75rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            color: var(--text-primary);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: var(--bg-tertiary);
            color: var(--primary-color);
        }
        
        /* Dropdown with icons */
        .dropdown-menu-icons {
            min-width: 200px;
        }
        
        .dropdown-menu-icons .dropdown-item {
            padding: 0.75rem 1.25rem;
            font-weight: 500;
        }
        
        .dropdown-menu-icons .dropdown-item:hover i {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        /* Page heading */
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-primary);
            font-weight: 700;
        }

        h1 i, h2 i, h3 i {
            color: var(--primary-color);
        }

        /* Stats card special styling */
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c61 100%);
            color: white;
            border: none;
        }

        .stat-card .card-body {
            padding: 2rem;
        }

        /* Modal */
        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
        }

        .modal-header {
            border-bottom: 2px solid var(--border-color);
        }

        .modal-title {
            color: var(--text-primary);
        }

        /* List group */
        .list-group-item {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        /* Text colors */
        .text-muted {
            color: var(--text-secondary) !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff8c61;
        }
    </style>

    @stack('styles')
    </head>
<body>
            @include('layouts.navigation')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
                    </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme Toggle Script -->
    <script>
        // Check for saved theme preference or default to 'dark'
        const currentTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);

        function toggleTheme() {
            const theme = document.documentElement.getAttribute('data-theme');
            const newTheme = theme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
        }

        // Set initial icon
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcon(currentTheme);
        });
    </script>
    
    @stack('scripts')
    </body>
</html>
