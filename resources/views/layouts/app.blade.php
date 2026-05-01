<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Passport Suvidha') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <!-- Datatable -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/buttons.dataTables.min.css') }}">

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js"></script>
    <style>
    :root {
        --primary-blue: #0D3B66;
        --secondary-blue: #1E5B94;
        --accent-blue: #2A77C9;
        --light-gray: #F3F4F6;
        --text-gray: #6B7280;
        --border-color: #E5E7EB;
    }

    /* Ensure font consistency */
    body {
        font-family: var(--font-family-sans);
    }

    /* Custom Scrollbar Styles */
    ::-webkit-scrollbar {
        width: 8px;
        /* Width of the entire scrollbar */
        height: 8px;
        /* Height of the horizontal scrollbar */
    }

    ::-webkit-scrollbar-track {
        background: var(--light-gray);
        /* Color of the tracking area */
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background-color: var(--secondary-blue);
        /* Color of the scroll thumb */
        border-radius: 10px;
        /* Roundness of the scroll thumb */
        border: 2px solid var(--light-gray);
        /* Creates padding around scroll thumb */
    }

    ::-webkit-scrollbar-thumb:hover {
        background-color: var(--primary-blue);
        /* Color of the scroll thumb on hover */
    }

    /* End Custom Scrollbar Styles */

    body {
        background: linear-gradient(135deg, var(--light-gray) 0%, var(--border-color) 100%);
        min-height: 100vh;
        position: relative;
    }

    .layout-wrapper {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    .sidebar {
        background: white;
        box-shadow: 0 4px 6px -1px rgba(13, 59, 102, 0.05),
            0 10px 15px -3px rgba(13, 59, 102, 0.1);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 300px;
        z-index: 40;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
    }

    .main-content {
        flex: 1;
        margin-left: 300px;
        min-height: 100vh;
        transition: margin-left 0.3s ease-in-out;
        width: auto;
    }

    .top-nav {
        background: white;
        position: sticky;
        top: 0;
        z-index: 30;
        box-shadow: 0 1px 3px 0 rgba(13, 59, 102, 0.05);
        width: 100%;
    }

    .top-nav .container {
        height: 100%;
    }

    .top-nav .flex {
        height: 100%;
    }

    .page-title {
        color: var(--primary-blue);
        font-weight: 600;
        font-size: 1.25rem;
        line-height: 1.75rem;
        letter-spacing: -0.025em;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .user-name {
        color: var(--text-gray);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .btn-primary {
        background-color: var(--primary-blue);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background-color: var(--secondary-blue);
    }

    .page-content {
        padding: 1rem;
    }

    .menu-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: transparent;
        border-radius: 0.375rem;
        color: var(--primary-blue);
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        -webkit-tap-highlight-color: transparent;
        outline: none;
        touch-action: manipulation;
        margin-right: 0.75rem;
    }

    .menu-toggle:hover {
        background-color: var(--light-gray);
    }

    .menu-toggle:focus {
        outline: 2px solid var(--accent-blue);
    }

    .menu-toggle:active {
        transform: scale(0.95);
    }

    .main-content.sidebar-collapsed {
        margin-left: 80px;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar.collapsed .nav-link-text,
    .sidebar.collapsed .sidebar-header span,
    .sidebar.collapsed .text-xs {
        display: none;
    }

    .sidebar.collapsed nav {
        padding: 1rem 0.5rem;
    }

    .sidebar.collapsed .nav-link {
        justify-content: center;
        padding: 0.75rem;
    }

    .sidebar.collapsed .nav-link svg {
        margin-right: 0;
    }

    .sidebar.collapsed .sidebar-header {
        justify-content: center;
        padding: 1rem 0.5rem;
    }

    .sidebar.collapsed .close-sidebar {
        display: none;
    }

    .sidebar nav {
        flex: 1;
        padding: 0.5rem;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--secondary-blue) var(--light-gray);
        scroll-behavior: smooth;
    }

    .sidebar nav::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar nav::-webkit-scrollbar-track {
        background: var(--light-gray);
        border-radius: 3px;
    }

    .sidebar nav::-webkit-scrollbar-thumb {
        background-color: var(--secondary-blue);
        border-radius: 3px;
    }

    .sidebar nav::-webkit-scrollbar-thumb:hover {
        background-color: var(--primary-blue);
    }

    /* Add styles for active state and hover effects */
    .nav-link {
        color: var(--text-gray);
        transition: all 0.2s ease;
        border-radius: 0.5rem;
        margin: 0.25rem 0.5rem;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .nav-link:hover {
        background-color: var(--light-gray);
        color: var(--primary-blue);
        transform: translateX(4px);
    }

    .nav-link.active {
        background-color: var(--primary-blue);
        color: white;
        font-weight: 500;
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background-color: var(--accent-blue);
        border-radius: 0 4px 4px 0;
    }

    .nav-link svg {
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .nav-link:hover svg {
        transform: scale(1.1);
    }

    .nav-link-text {
        margin-left: 0.75rem;
        transition: all 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        background-color: var(--primary-blue);
        min-height: 64px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .sidebar-title-text {
        font-size: 1.25rem;
        font-weight: 600;
        letter-spacing: -0.025em;
        color: white;
        transition: all 0.3s ease;
    }

    .close-sidebar {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        outline: none;
    }

    .close-sidebar:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .close-sidebar:active {
        transform: translateY(-50%) scale(0.95);
    }

    .sidebar-section-title {
        color: var(--text-gray);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem 0.5rem;
        margin-top: 0.5rem;
        transition: all 0.3s ease;
    }

    .sidebar.collapsed .sidebar-section-title {
        display: none;
    }

    /* Add these styles in the style block before the media queries */
    [x-cloak] {
        display: none !important;
    }

    .transition {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .transform {
        transform: translateX(0);
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
            position: fixed;
            z-index: 50;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .menu-toggle {
            display: flex;
        }

        .page-content {
            padding: 0.75rem;
        }
    }

    @media (max-width: 768px) {
        .top-nav {
            padding: 0.5rem;
        }

        .page-title {
            font-size: 1.1rem;
        }

        .user-info {
            gap: 0.75rem;
        }

        .user-name {
            font-size: 0.85rem;
        }

        .btn-primary {
            padding: 0.375rem 1rem;
            font-size: 0.85rem;
        }

        .nav-link {
            padding: 0.5rem 0.75rem;
        }

        .sidebar-header {
            padding: 1rem;
        }
    }

    @media (max-width: 640px) {
        .top-nav .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .user-name {
            display: none;
        }

        .page-title {
            font-size: 1rem;
        }

        .nav-link {
            padding: 0.375rem 0.5rem;
        }

        .nav-link-text {
            font-size: 0.875rem;
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            padding: 0.75rem 1rem 0.25rem;
        }
    }

    /* Mobile Menu Overlay */
    @media (max-width: 1024px) {
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }

        .sidebar-overlay.active {
            display: block;
        }
    }
    </style>
</head>

<body class="font-sans antialiased" x-data="{
    // Initialize state from localStorage, default to false (expanded)
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || false,
    isMobileMenuOpen: false,

    toggleSidebar() {
        if (window.innerWidth <= 1024) {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
            document.body.style.overflow = this.isMobileMenuOpen ? 'hidden' : '';
        } else {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    },

    // Function to scroll to active element
    scrollToActive() {
        // Wait for the DOM to be fully loaded
        setTimeout(() => {
            const activeElement = document.querySelector('.nav-link.active');
            if (activeElement) {
                const sidebarNav = document.querySelector('.sidebar nav');
                const sidebarRect = sidebarNav.getBoundingClientRect();
                const activeRect = activeElement.getBoundingClientRect();
                
                // Calculate the scroll position needed to center the active element
                const scrollPosition = activeRect.top - sidebarRect.top - (sidebarRect.height / 2) + (activeRect.height / 2);
                
                // Smooth scroll to the active element
                sidebarNav.scrollTo({
                    top: scrollPosition,
                    behavior: 'smooth'
                });
            }
        }, 100);
    }
}" x-init="console.log('Alpine initialized, sidebar state from localStorage:', localStorage.getItem('sidebarCollapsed')); scrollToActive()"
    x-cloak>
    <div class="layout-wrapper">
        <div class="sidebar-overlay" :class="{ 'active': isMobileMenuOpen }" @click="toggleSidebar()"></div>
        <main class="main-content" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
            <nav class="top-nav h-16 border-b border-border-color">
                <div class="mx-auto h-full px-6">
                    <div class="flex justify-between items-center h-full">
                        <div class="flex items-center flex-grow">
                            <button type="button" class="menu-toggle" @click="toggleSidebar()"
                                aria-label="Toggle Sidebar">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            <h2 class="page-title">@yield('title', 'Dashboard')</h2>
                        </div>
                        <div class="user-info">
                            @auth
                            <span class="user-name">Hello, <span
                                    class="font-bold">{{ Auth::user()->name }}</span></span>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="btn-primary">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <div class="page-content">
                @yield('content')
            </div>
        </main>

        <aside class="sidebar" :class="{ 'collapsed': sidebarCollapsed, 'active': isMobileMenuOpen }">
            <div class="sidebar-header">
                <div class="flex items-center">
                    <span
                        class="text-xl font-semibold text-white sidebar-title-text">{{ config('app.name', 'Passport Suvidha') }}</span>
                </div>
            </div>

            <nav class="mt-6 px-3">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Dashboard' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="nav-link-text">Dashboard</span>
                </a>

                <a href="{{ route('admin.todaystatistics') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.todaystatistics') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Today Statistics' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Today Statistics</span>
                </a>

                <a href="{{ route('admin.customer.search.form') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.customer.search.form') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Search Customer' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="nav-link-text">Search Customer</span>
                </a>

                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">CUSTOMER MANAGEMENT</div>

                <a href="{{ route('admin.leads.today') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.leads.today') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Today\'s Leads' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"></line>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"></line>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"></line>
                        <circle cx="12" cy="15" r="2" stroke-width="2"></circle>
                        <path stroke-width="2" d="M8 20c1.5-2 6.5-2 8 0"></path>
                    </svg>
                    <span class="nav-link-text">Today's Leads</span>
                </a>

                <a href="{{ route('admin.leads.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.leads.index') && !request()->routeIs('admin.leads.today') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Leads' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="8" r="3" stroke-width="2"></circle>
                        <circle cx="17" cy="8" r="3" stroke-width="2"></circle>
                        <path stroke-width="2" d="M5 20c0-3 4-4 4-4s4 1 4 4"></path>
                        <path stroke-width="2" d="M13 20c0-3 4-4 4-4s4 1 4 4"></path>
                    </svg>
                    <span class="nav-link-text">Leads</span>
                </a>

                <a href="{{ route('admin.customers.today') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.customers.today') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Today\'s Customers' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Today's Customers</span>
                </a>

                <a href="{{ route('admin.customers.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.customers.index') && !request()->routeIs('admin.customers.today') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Customers' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Customers</span>
                </a>

                <a href="{{ route('admin.customers.create') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.customers.create') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Create An Account' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    <span class="nav-link-text">Create An Account</span>
                </a>

                {{-- Documents Section --}}
                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">DOCUMENTS</div>

                <a href="{{ route('admin.application-documents.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.application-documents.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Application Documents' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Application Documents</span>
                </a>

                <a href="{{ route('admin.final-details.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.final-details.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Final Details' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="9" y="2" width="6" height="4" rx="1" ry="1" stroke-width="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 4H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2h-2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6" />
                    </svg>
                    <span class="nav-link-text">Final Details</span>
                </a>

                <a href="{{ route('admin.appointment-letters.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.appointment-letters.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Appointment Letters' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5h6m2 0a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2m2 0a2 2 0 104 0 2 2 0 00-4 0zm-1 9l2 2 4-4">
                        </path>
                    </svg>
                    <span class="nav-link-text">Appointment Letters</span>
                </a>

                {{-- Application Section --}}
                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">APPLICATION</div>

                <a href="{{ route('admin.application.status') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.application.status') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'All Applications' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m-6-8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                    <span class="nav-link-text">All Applications</span>
                </a>

                <a href="{{ route('admin.application.status.new') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.application.status.new') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'New Applications' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4V4z" />
                    </svg>
                    <span class="nav-link-text">New Applications</span>
                </a>

                <a href="{{ route('admin.application.status.current') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.application.status.current') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Current Applications' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                        <circle cx="12" cy="12" r="9" stroke-width="2" />
                    </svg>
                    <span class="nav-link-text">Current Applications</span>
                </a>

                <a href="{{ route('admin.application.status.completed') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.application.status.completed') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Completed Applications' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5h6m2 0a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h2z" />
                    </svg>
                    <span class="nav-link-text">Completed Applications</span>
                </a>

                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">SYSTEM</div>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">OTP Management</span>
                </a>

                <a href="{{ route('admin.invoices.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Invoice List' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6M9 8h6m2 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v9a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Invoice List</span>
                </a>

                <a href="{{ route('admin.dnd.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.dnd.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'DND List' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="2"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5l14 14"></path>
                    </svg>
                    <span class="nav-link-text">DND List</span>
                </a>

                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">OFFER</div>

                <a href="{{ route('admin.card-offers.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.card-offers.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Card Offer' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"></line>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 15h2m4 0h4"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3l5 5-5 5"></path>
                    </svg>
                    <span class="nav-link-text">Card Offer</span>
                </a>

                <a href="{{ route('admin.star-offers.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.star-offers.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Star Offer' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"></line>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 15h2m4 0h4"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3l5 5-5 5"></path>
                    </svg>
                    <span class="nav-link-text">Star Offer</span>
                </a>

                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">PAYMENT GATEWAY LOG</div>

                <a href="{{ route('admin.razorpay-logs.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.razorpay-logs.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Razorpay Log' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"></line>
                    </svg>
                    <span class="nav-link-text">Razorpay Log</span>
                </a>

                <a href="{{ route('admin.cashfree-logs.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.cashfree-logs.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Cashfree Log' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"></line>
                    </svg>
                    <span class="nav-link-text">Cashfree Log</span>
                </a>

                <a href="{{ route('admin.zaakpay-logs.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.zaakpay-logs.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Zaakpay Log' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"></line>
                    </svg>
                    <span class="nav-link-text">Zaakpay Log</span>
                </a>

                {{-- Support Section --}}
                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">SUPPORT</div>

                <a href="{{ route('admin.support.customer') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.support.customer') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Customer Support' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 0a3 3 0 104.243 4.243m-4.243-4.243L10.586 1.414m7.778 4.222a3 3 0 00-4.243 0M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                        </path>
                    </svg>
                    <span class="nav-link-text">Customer Support</span>
                </a>

                <a href="{{ route('admin.support.guest') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.support.guest') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Guest Support' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Guest Support</span>
                </a>

                <!-- <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">SMS</div>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Custom Sms</span>
                </a>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Sms Message</span>
                </a>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Remarketing Log</span>
                </a> -->

                {{-- New Templates Section --}}
                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">TEMPLATES</div>

                <a href="{{ route('admin.predefined-messages.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.predefined-messages.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Predefined Messages' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-5-5h11.5a2.5 2.5 0 002.5-2.5V7.5a2.5 2.5 0 00-2.5-2.5H5.5A2.5 2.5 0 003 7.5v7.008A2.492 2.492 0 005.5 17H7z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Predefined Messages</span>
                </a>

                <a href="{{ route('admin.document-types.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.document-types.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Document Types' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Document Types</span>
                </a>

                <!-- <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">OTHER OPTIONS</div>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Important Update</span>
                </a>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Site Options</span>
                </a>

                <a href="{{ route('admin.otps.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.otps.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'OTP Management' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Pages</span>
                </a> -->

                {{-- Staff Management Section --}}
                @if (Auth::check() && Auth::user()->isAdmin())
                <div class="mt-6 px-4 py-2 text-xs font-semibold text-text-gray">STAFF MANAGEMENT</div>

                <a href="{{ route('admin.users.index') }}"
                    class="nav-link flex items-center px-4 py-3 rounded-lg mb-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    x-bind:title="sidebarCollapsed ? 'Staff Members' : ''">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="nav-link-text">Staff Members</span>
                </a>
                @endif


                {{-- End New Templates Section --}}
            </nav>
        </aside>
    </div>

    <!-- Datatable -->
    <script src="{{ asset('vendor/datatables/js/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/vfs_fonts.js') }}"></script>

    <!-- Toastify -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('js/toast.js') }}"></script>
    @include('components.toast')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/confirm-actions.js') }}"></script>

    @stack('scripts')
</body>

</html>