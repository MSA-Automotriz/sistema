<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
            x-data="{
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: window.innerWidth >= 768,
                collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                activeMenu: null,
                isMobile() { return window.innerWidth < 768; },
                isActive(route) {
                    const currentPath = window.location.pathname;
                    if (route === 'admin/ventas/cotizaciones/dashboard') {
                        return currentPath === '/admin/ventas/cotizaciones/dashboard';
                    }
                    if (route === 'admin/ventas/cotizaciones') {
                        return currentPath.includes(route) && !currentPath.includes('/admin/ventas/cotizaciones/dashboard');
                    }
                    return currentPath.includes(route);
                },
                shouldBeOpen(routes) { return routes.some(route => window.location.pathname.includes(route)); },
                isExactActive(path) { return window.location.pathname === `/${path}`; },
                isAnyActive(paths) { return paths.some(path => window.location.pathname.startsWith(`/${path}`)); },
                toggleSidebar() {
                    if (this.isMobile()) {
                        this.sidebarOpen = !this.sidebarOpen;
                    } else {
                        this.collapsed = !this.collapsed;
                        localStorage.setItem('sidebarCollapsed', this.collapsed);
                    }
                }
            }"
            x-init="
                $watch('darkMode', val => localStorage.setItem('darkMode', val));
                $watch('collapsed', val => localStorage.setItem('sidebarCollapsed', val));
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 768) {
                        sidebarOpen = true;
                    } else {
                        sidebarOpen = false;
                    }
                });
                activeMenu = shouldBeOpen(['admin/ventas']) ? 'ventas' :
                    shouldBeOpen(['admin/clientes']) ? 'clientes' :
                    shouldBeOpen(['admin/compras']) ? 'compras' :
                    shouldBeOpen(['admin/almacenes/partes', 'admin/productos-servicios/servicios', 'admin/productos-servicios/vehiculos', 'admin/estrategia']) ? 'productos-servicios' :
                    shouldBeOpen(['admin/inventario', 'admin/inventario/traslados', 'admin/inventario/movimientos', 'admin/kardex']) ? 'inventario' :
                    shouldBeOpen(['admin/mantenimiento']) ? 'mantenimiento' :
                    shouldBeOpen(['admin/usuarios']) ? 'usuarios' :
                    shouldBeOpen(['admin/configuracion']) ? 'configuracion' : null;
            "
            :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MSA Automotriz') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Styles -->
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-height {
            height: 100vh;
        }
        .notification-width {
            width: 300px;
        }
        
        /* Estilo para tema oscuro */
        .dark {
            --bs-body-color: #f8f9fa;
            --bs-body-bg: #0f172a;
        }
        
        .dark .bg-dark-custom {
            background-color: #0f172a !important;
        }
        
        .dark .text-dark-custom {
            color: #f8fafc;
        }
        
        .dark .border-dark-custom {
            border-color: #1e293b;
        }
        
        .sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
            background-color: #0f172a !important; /* Premium dark background always */
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            border-right: 1px solid #1e293b !important;
        }

        /* Sidebar encima solo en móvil */
        @media (max-width: 767.98px) {
            .sidebar {
                z-index: 1060;
                width: 16rem !important;
                left: 0;
                top: 0;
                bottom: 0;
                position: fixed;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
            }
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
                transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            body.sidebar-collapsed .main-content {
                margin-left: 5rem !important;
                width: calc(100% - 5rem) !important;
            }
            body.sidebar-expanded .main-content {
                margin-left: 16rem !important;
                width: calc(100% - 16rem) !important;
            }
        }
        
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
        }
        
        /* Estilos para el menú de usuario */
        .user-dropdown {
            position: absolute;
            bottom: 100%;
            left: 0;
            z-index: 1000;
            margin-bottom: 0.5rem;
        }
        
        /* Mejoras en el scroll del sidebar */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.2) transparent;
        }
        
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.2);
            border-radius: 4px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(100, 116, 139, 0.5);
        }

        /* Sin bordes para botones de toggle */
        .btn-no-border {
            border: none;
            box-shadow: none;
        }
        
        .btn-no-border:focus, .btn-no-border:active {
            box-shadow: none;
            outline: none;
        }
        
        /* OVERRIDES: Forzar Sidebar a ser Dark ignorando clases de HTML/Alpine */
        .sidebar .nav-link, .sidebar .dropdown-item {
            color: #94a3b8 !important; /* slate-400 */
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover, .sidebar .dropdown-item:hover, .sidebar .hover-light:hover {
            background-color: #1e293b !important; /* slate-800 */
            color: #f8fafc !important; /* platinium white */
        }

        /* Ajuste para el logo */
        .logo-img {
            height: auto;
            width: 4rem;
        }
        
        .sidebar h5 {
            color: #ffffff !important;
        }
        
        .sidebar .text-dark, .sidebar .text-secondary {
            color: #94a3b8 !important;
        }
        
        .sidebar .border-bottom {
            border-bottom-color: #1e293b !important;
        }
        
        a{
            text-decoration:none;
        }
        
        /* Estilo para ítem de menú activo */
        .sidebar .menu-active {
            background-color: #1e3a8a !important; /* blue-900 */
            color: #bfdbfe !important; /* blue-200 */
            font-weight: 600 !important;
        }
        
        /* Estilos para icono activo */
        .sidebar .menu-icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.6rem;
            transition: all 0.3s ease;
            color: #64748b !important;
        }
        
        .sidebar .icon-active {
            background-color: #2563eb !important; /* blue-600 */
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }
        
        .sidebar .icon-active svg, .sidebar .icon-active i {
            color: white !important;
        }
        
        /* Estilo para submenú activo */
        .sidebar .submenu-active {
            font-weight: 600 !important;
            color: #60a5fa !important;
            background-color: transparent !important;
        }

        /* Utilidades adicionales */
        .smaller {
            font-size: 0.8rem;
        }

        .text-light-50 {
            color: rgba(248, 249, 250, 0.8);
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .transition {
            transition: all 0.3s ease;
        }

        .hover-light:hover {
            background-color: #f8fafc;
        }

        .hover-dark:hover {
            background-color: #1e293b;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
        
        /* Main content responsive adjustments */
        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Fix for notification badges */
        .badge-notification {
            font-size: 0.6rem;
            font-weight: 500;
            padding: 0.25em 0.5em;
        }

        /* --- Diseño Premium Corporativo Global (Paneles, Dashboard, Cotizaciones) --- */
        .dashboard-hero {
            background-color: #0f172a; /* slate-900 */
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            color: white;
            padding: 3.5rem 2rem;
            margin: -1.5rem -1.5rem 2.5rem -1.5rem;
            border-radius: 0 0 1.5rem 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero-glow {
            position: absolute;
            width: 350px;
            height: 350px;
            background: #2563eb;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.15;
            top: -100px;
            right: -50px;
            pointer-events: none;
        }
        
        .hero-glow-alt {
            position: absolute;
            width: 300px;
            height: 300px;
            background: #06b6d4;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.1;
            bottom: -150px;
            left: -50px;
            pointer-events: none;
        }

        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.05);
            background: #ffffff;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02) !important;
        }
        
        .stat-icon-wrapper {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .dashboard-card {
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px -1px rgba(0,0,0,0.05);
            border-radius: 1.25rem;
        }
        
        .dashboard-card .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 1.25rem 1.25rem 0 0;
            padding: 1.25rem 1.5rem;
        }
        
        .quick-action-btn {
            transition: all 0.2s ease;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #334155;
            text-decoration: none;
            display: block;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-3px);
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            color: #0f172a;
        }
        
        .activity-number {
            font-size: 2.25rem;
            letter-spacing: -1px;
        }
        
        .dark .dashboard-hero {
            border-bottom: 1px solid #1e293b;
        }
        
        .dark .stat-card, .dark .dashboard-card {
            background: #1e293b;
            border-color: #334155;
        }
        
        .dark .dashboard-card .card-header {
            border-bottom-color: #334155;
        }

        .dark .quick-action-btn {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        
        .dark .quick-action-btn:hover {
            background: #1e293b;
            border-color: #475569;
            color: #ffffff;
        }
        
        .dark .bg-light {
            background-color: #0f172a !important; /* Para fondos que emulan bg-light globalmente en dark */
        }

        /* --- Optimizaciones de Responsividad Móvil y Tablets --- */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 4px;
        }

        @media (max-width: 767.98px) {
            .dashboard-hero {
                padding: 2rem 1rem !important;
                margin: -1rem -1rem 1.5rem -1rem !important;
                border-radius: 0 0 1rem 1rem !important;
            }

            .stat-card {
                margin-bottom: 0.75rem;
            }

            .main-content {
                padding: 1rem !important;
                padding-top: 4rem !important; /* Spacing for mobile menu toggle button */
            }

            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .table th, .table td {
                white-space: nowrap;
                font-size: 0.875rem;
            }

            .btn {
                min-height: 38px;
            }

            .form-control, .form-select {
                min-height: 42px;
                font-size: 0.95rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="min-vh-100 transition bg-light" :class="darkMode ? 'dark bg-dark text-light' : 'bg-light'">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen && isMobile()"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="sidebar-overlay"
            :class="{ 'show': sidebarOpen && isMobile() }"></div>

    <div class="d-flex min-vh-100">
        <!-- Mobile Menu Button -->
        <button @click="toggleSidebar()" 
                class="btn btn-primary position-fixed d-md-none"
                style="top: 1rem; left: 1rem; z-index: 1060; border-radius: 0.5rem;"
                :class="sidebarOpen ? 'd-none' : 'd-block'">
            <i class="fas fa-bars"></i>
        </button>

           <!-- Sidebar -->
           <div class="sidebar position-fixed top-0 bottom-0 start-0 bg-white shadow-lg"
               :class="[sidebarOpen ? 'show' : '', darkMode ? 'bg-dark-custom' : '', collapsed && !isMobile() ? 'sidebar-collapsed' : 'sidebar-expanded']">
            <div class="d-flex flex-column sidebar-height">
                <!-- Sidebar Header -->
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom" :class="darkMode ? 'border-dark-custom' : ''">
                    <div class="d-flex align-items-center">
                        <img :class="{ 'me-3': !collapsed || isMobile() }" 
                             class="logo-img" 
                             src="{{ asset('img/logo-MSA.png') }}" 
                             alt="MSA Automotriz">
                        <h5 x-show="!collapsed || isMobile()" 
                            class="mb-0 fw-bold" 
                            :class="darkMode ? 'text-light' : 'text-dark'">
                            MSA
                        </h5>
                    </div>
                    
                    <!-- Mobile close button -->
                    <button @click="sidebarOpen = false" 
                            class="btn btn-sm btn-no-border d-md-none" 
                            :class="darkMode ? 'text-light' : 'text-secondary'">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <!-- Desktop collapse button -->
                    <button @click="toggleSidebar()" 
                            class="btn btn-sm btn-no-border d-none d-md-block" 
                            :class="darkMode ? 'text-light' : 'text-secondary'">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             :class="{ 'rotate-180': !collapsed }" 
                             class="transition" 
                             style="height: 1.25rem; width: 1.25rem;" 
                             fill="none" 
                             viewBox="0 0 24 24" 
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex-grow-1 overflow-auto py-3 sidebar-scroll">
                    <ul class="nav flex-column px-2">
                        @if(auth()->check() && auth()->user()->hasPermission('dashboard'))
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" 
                            class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded" 
                            :class="[
                                darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                isActive('admin/dashboard') ? 'menu-active' : ''
                            ]">
                                <div class="menu-icon-container me-2" :class="isActive('admin/dashboard') ? 'icon-active' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <span x-show="!collapsed || isMobile()">Dashboard</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('ventas'))
                        <!-- Ventas -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'ventas') ? null : 'ventas'" 
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/ventas') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/ventas') ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Ventas</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'ventas' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeMenu === 'ventas' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.ventas.cotizaciones.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/ventas/cotizaciones') ? 'submenu-active' : ''
                                ]">
                                    Cotizaciones
                                </a>

                                <a href="{{ route('admin.ventas.pos.index') }}" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'" title="Terminal POS">
                                    <i class="fas fa-cash-register me-2"></i> Terminal POS
                                </a>

                                <a href="{{ route('admin.ventas.pos.ventas') }}" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'" title="Gestión de Ventas">
                                    <i class="fas fa-list-alt me-2"></i> Gestión de Ventas
                                </a>
                                <div x-data="{ openSub: shouldBeOpen(['admin/ventas/ventas']) }" x-init="openSub = shouldBeOpen(['admin/ventas/ventas'])">
                                    <button @click="openSub = !openSub" 
                                            class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 rounded text-start small btn-no-border" 
                                            :class="[
                                                darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                                isActive('admin/ventas/ventas') ? 'submenu-active' : ''
                                            ]">
                                        <span>Ventas</span>
                                        <svg :class="{ 'rotate-180': openSub }" class="ms-2" style="height: 0.75rem; width: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="openSub" class="ps-4">
                                        <a href="#" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'">
                                            Guías de entrega
                                            <small class="ms-2 d-inline-flex align-items-center">
                                                <span class="badge bg-danger badge-notification rounded-pill">Prox</span>
                                            </small>
                                        </a>
                                        <a href="#" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'">
                                            Vales de devolución
                                            <small class="ms-2 d-inline-flex align-items-center">
                                                <span class="badge bg-danger badge-notification rounded-pill">Prox</span>
                                            </small>
                                        </a>
                                        <a href="#" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'">
                                            Recepción
                                            <small class="ms-2 d-inline-flex align-items-center">
                                                <span class="badge bg-danger badge-notification rounded-pill">Prox</span>
                                            </small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('clientes'))
                        <!-- Clientes -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'clientes') ? null : 'clientes'" 
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/clientes') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/clientes') ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Clientes</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'clientes' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeMenu === 'clientes' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.clientes.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/clientes') && !isActive('admin/clientes/categorias') ? 'submenu-active' : ''
                                ]">
                                    Clientes
                                </a>
                                <a href="{{ route('admin.clientes.categorias.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/clientes/categorias') ? 'submenu-active' : ''
                                ]">
                                    Categorías de Clientes
                                </a>
                            </div>
                        </li>
                        @endif
                        
                        @if(auth()->check() && auth()->user()->hasPermission('compras'))
                        <!-- Compras -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'compras') ? null : 'compras'" 
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/compras') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/compras') ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Compras</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'compras' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeMenu === 'compras' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.compras.requerimientos.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/compras/requerimientos') ? 'submenu-active' : ''
                                ]">Requerimientos</a>
                                <a href="{{ route('admin.compras.ordenes.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/compras/ordenes') ? 'submenu-active' : ''
                                ]">Órdenes de compra</a>

                                <div x-data="{ openSub: shouldBeOpen(['admin/compras/documentos']) }" x-init="openSub = shouldBeOpen(['admin/compras/documentos'])">
                                    <button @click="openSub = !openSub" 
                                            class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 rounded text-start small btn-no-border position-relative" 
                                            :class="[
                                                darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                                isActive('admin/compras/documentos') ? 'submenu-active' : ''
                                            ]">
                                        <span>Documentos</span>
                                        <svg :class="{ 'rotate-180': openSub }" class="ms-2" style="height: 0.75rem; width: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="openSub" class="ps-4">
                                        <a href="{{ route('admin.recepcion.index') }}" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'">
                                            Recepción
                                        </a>
                                        <a href="{{ route('admin.guias.index') }}" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'">
                                            Guías de entrega
                                        </a>
                                        <a href="{{ route('admin.devoluciones.index') }}" class="nav-link d-block py-2 px-3 rounded small position-relative" :class="darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light'">
                                            Vales de devolución
                                        </a>
                                    </div>
                                </div>
                                <div x-data="{ openSub: shouldBeOpen(['admin/compras/proveedores']) }" x-init="openSub = shouldBeOpen(['admin/compras/proveedores'])">
                                    <button @click="openSub = !openSub"
                                            class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 rounded text-start small btn-no-border"
                                            :class="[
                                                darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                                isActive('admin/compras/proveedores') ? 'submenu-active' : ''
                                            ]">
                                        <span>Proveedores</span>
                                        <svg :class="{ 'rotate-180': openSub }" class="ms-2" style="height: 0.75rem; width: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="openSub" class="ps-4">
                                        <a href="{{ route('admin.compras.proveedores.index') }}"
                                        class="nav-link d-block py-2 px-3 rounded small"
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            window.location.pathname === '/admin/compras/proveedores' || window.location.pathname.startsWith('/admin/compras/proveedores/') && !window.location.pathname.includes('/categorias') ? 'submenu-active' : ''
                                        ]">Proveedores</a>
                                        <a href="{{ route('admin.compras.proveedores.categorias.index') }}"
                                        class="nav-link d-block py-2 px-3 rounded small"
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            window.location.pathname.includes('/admin/compras/proveedores/categorias') ? 'submenu-active' : ''
                                        ]">Categorías</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('almacenes'))
                        <!-- Productos/Servicios (antes Catálogos) -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'productos-servicios') ? null : 'productos-servicios'" 
                                class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                :class="[
                                    darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                    isActive('admin/almacenes/partes') || isActive('admin/productos-servicios/servicios') || isActive('admin/productos-servicios/vehiculos') || isActive('admin/estrategia') ? 'menu-active' : ''
                                ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/almacenes/partes') || isActive('admin/productos-servicios/servicios') || isActive('admin/productos-servicios/vehiculos') || isActive('admin/estrategia') ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Productos/Servicios</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'productos-servicios' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeMenu === 'productos-servicios' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <div x-data="{ openSub: shouldBeOpen(['admin/almacenes/partes']) }" x-init="openSub = shouldBeOpen(['admin/almacenes/partes'])">
                                    <button @click="openSub = !openSub" 
                                            class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 rounded text-start small btn-no-border" 
                                            :class="[
                                                darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                                isActive('admin/almacenes/partes') ? 'submenu-active' : ''
                                            ]">
                                        <span>Partes / Repuestos</span>
                                        <svg :class="{ 'rotate-180': openSub }" class="ms-2" style="height: 0.75rem; width: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="openSub" class="ps-4">
                                        <a href="{{ route('admin.almacenes.partes.index') }}" 
                                        class="nav-link d-block py-2 px-3 rounded small" 
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            isActive('admin/almacenes/partes') && !isActive('admin/almacenes/partes/categorias') ? 'submenu-active' : ''
                                        ]">Items</a>
                                        <a href="{{ route('admin.almacenes.partes.categorias.index') }}" 
                                        class="nav-link d-block py-2 px-3 rounded small" 
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            isActive('admin/almacenes/partes/categorias') ? 'submenu-active' : ''
                                        ]">Categorías</a>
                                    </div>
                                </div>
                                
                                <div x-data="{ openSub: shouldBeOpen(['admin/productos-servicios/servicios']) }" x-init="openSub = shouldBeOpen(['admin/productos-servicios/servicios'])">
                                    <button @click="openSub = !openSub" 
                                            class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 rounded text-start small btn-no-border" 
                                            :class="[
                                                darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                                isActive('admin/productos-servicios/servicios') ? 'submenu-active' : ''
                                            ]">
                                        <span>Servicios</span>
                                        <svg :class="{ 'rotate-180': openSub }" class="ms-2" style="height: 0.75rem; width: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="openSub" class="ps-4">
                                        <a href="{{ route('admin.productos-servicios.servicios.index') }}"
                                        class="nav-link d-block py-2 px-3 rounded small"
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            isActive('admin/productos-servicios/servicios') && !isActive('admin/productos-servicios/servicios/categorias') ? 'submenu-active' : ''
                                        ]">
                                        Items
                                        </a>

                                        <a href="{{ route('admin.productos-servicios.servicios.categorias.index') }}"
                                        class="nav-link d-block py-2 px-3 rounded small"
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            isActive('admin/productos-servicios/servicios/categorias') ? 'submenu-active' : ''
                                        ]">
                                        Categorías
                                        </a>
                                    </div>
                                </div>

                                <a href="{{ route('admin.productos-servicios.vehiculos.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/productos-servicios/vehiculos') ? 'submenu-active' : ''
                                ]">Vehículos</a>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('inventario'))
                        <!-- Inventario -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'inventario') ? null : 'inventario'"
                                class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                :class="[
                                    darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                    activeMenu === 'inventario' || isAnyActive([
                                        'admin/inventario',
                                        'admin/inventario/traslados',
                                        'admin/inventario/movimientos',
                                        'admin/inventario/devoluciones',
                                        'admin/inventario/kardex'
                                    ]) ? 'menu-active' : ''
                                ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2"
                                        :class="(activeMenu === 'inventario' || isAnyActive([
                                            'admin/inventario',
                                            'admin/inventario/traslados',
                                            'admin/inventario/movimientos',
                                            'admin/inventario/devoluciones',
                                            'admin/inventario/kardex'
                                        ])) ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Inventario</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'inventario' }" class="ms-2"
                                    style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="activeMenu === 'inventario' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.inventario.index') }}"
                                    class="nav-link d-block py-2 px-3 rounded small"
                                    :class="[
                                        darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                        isExactActive('admin/inventario') ? 'submenu-active' : ''
                                    ]">
                                    Reporte Inventario
                                </a>

                                <a href="{{ route('admin.inventario.movimientos.index') }}"
                                    class="nav-link d-block py-2 px-3 rounded small"
                                    :class="[
                                        darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                        isExactActive('admin/inventario/movimientos') ? 'submenu-active' : ''
                                    ]">
                                    Movimientos
                                </a>

                                <a href="{{ route('admin.inventario.traslados.index') }}"
                                    class="nav-link d-block py-2 px-3 rounded small"
                                    :class="[
                                        darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                        isExactActive('admin/inventario/traslados') ? 'submenu-active' : ''
                                    ]">
                                    Traslados
                                </a>

                                <a href="{{ route('admin.inventario.devoluciones.index') }}"
                                    class="nav-link d-block py-2 px-3 rounded small"
                                    :class="[
                                        darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                        isExactActive('admin/inventario/devoluciones') ? 'submenu-active' : ''
                                    ]">
                                    Devolución a proveedor
                                </a>

                                <a href="{{ route('admin.inventario.kardex.form') }}"
                                    class="nav-link d-block py-2 px-3 rounded small"
                                    :class="[
                                        darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                        isExactActive('admin/inventario/kardex') ? 'submenu-active' : ''
                                    ]">
                                    Reporte Kardex
                                </a>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('reportes'))
                        <!-- Reportes -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'reportes') ? null : 'reportes'"
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/reportes') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/reportes') ? 'icon-active' : ''">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Reportes</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'reportes' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="activeMenu === 'reportes' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.reportes.mantenimiento') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/reportes/mantenimiento') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-tools me-2"></i>Mantenimiento
                                </a>

                                <a href="{{ route('admin.reportes.ventas') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/reportes/ventas') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-chart-line me-2"></i>Ventas
                                </a>

                                <a href="{{ route('admin.reportes.compras') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/reportes/compras') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-shopping-cart me-2"></i>Compras
                                </a>

                                <a href="{{ route('admin.reportes.inventario') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/reportes/inventario') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-boxes me-2"></i>Inventario
                                </a>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('mantenimiento'))
                        <!-- Mantenimiento -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'mantenimiento') ? null : 'mantenimiento'"
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/mantenimiento') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/mantenimiento') ? 'icon-active' : ''">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Mantenimiento</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'mantenimiento' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="activeMenu === 'mantenimiento' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <!-- Dashboard -->
                                <a href="{{ route('admin.mantenimiento.dashboard') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/mantenimiento/dashboard') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>

                                <!-- Citas -->
                                <a href="{{ route('admin.mantenimiento.citas.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/mantenimiento/citas') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-calendar-alt me-2"></i>Citas
                                </a>

                                <!-- Órdenes de Trabajo -->
                                <a href="{{ route('admin.mantenimiento.ordenes.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/mantenimiento/ordenes') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-tools me-2"></i>Órdenes de Trabajo
                                </a>

                                <!-- Planes de Mantenimiento -->
                                <a href="{{ route('admin.planes-mantenimiento.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/planes-mantenimiento') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-wrench me-2"></i>Planes de Mantenimiento
                                </a>

                                <!-- Configuración -->
                                <div x-data="{ openSub: shouldBeOpen(['admin/productos-servicios/servicios', 'admin/mantenimiento/tecnicos']) }" x-init="openSub = shouldBeOpen(['admin/productos-servicios/servicios', 'admin/mantenimiento/tecnicos'])">
                                    <button @click="openSub = !openSub"
                                            class="nav-link d-flex align-items-center justify-content-between w-100 py-2 px-3 rounded small btn-no-border"
                                            :class="[
                                                darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                                (isActive('admin/productos-servicios/servicios') || isActive('admin/mantenimiento/tecnicos')) ? 'submenu-active' : ''
                                            ]">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-cog me-2"></i>Configuración
                                        </div>
                                        <svg :class="{ 'rotate-180': openSub }" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="openSub" class="ms-3">
                                        <a href="{{ route('admin.mantenimiento.tecnicos.index') }}"
                                        class="nav-link d-block py-2 px-3 rounded smaller"
                                        :class="[
                                            darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                            isActive('admin/mantenimiento/tecnicos') ? 'submenu-active' : ''
                                        ]">
                                            <i class="fas fa-user-cog me-2"></i>Técnicos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('almacenes'))
                        <!-- Almacenes -->
                        <li class="nav-item">
                            <a href="{{ route('admin.almacenes.index') }}"
                            class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded"
                            :class="[
                                darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                isActive('admin/almacenes') && !isActive('admin/almacenes/partes') && !isActive('admin/productos-servicios/servicios') && !isActive('admin/almacenes/vehiculos') && !isActive('admin/inventario/movimientos') ? 'menu-active' : ''
                            ]">
                                <div class="menu-icon-container me-2" :class="isActive('admin/almacenes') && !isActive('admin/almacenes/partes') && !isActive('admin/productos-servicios/servicios') && !isActive('admin/almacenes/vehiculos') && !isActive('admin/inventario/movimientos') ? 'icon-active' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span x-show="!collapsed || isMobile()">Almacenes</span>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && auth()->user()->hasPermission('configuracion'))
                        <!-- Establecimientos -->
                        <li class="nav-item">
                            <a href="{{ route('admin.establecimientos.index') }}"
                            class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded"
                            :class="[
                                darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                isActive('admin/establecimientos') ? 'menu-active' : ''
                            ]">
                                <div class="menu-icon-container me-2" :class="isActive('admin/establecimientos') ? 'icon-active' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span x-show="!collapsed || isMobile()">Establecimientos</span>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && auth()->user()->hasPermission('mantenimiento'))
                        <!-- Talleres -->
                        <li class="nav-item">
                            <a href="{{ route('admin.talleres.index') }}" 
                            class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded" 
                            :class="[
                                darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                isActive('admin/talleres') ? 'menu-active' : ''
                            ]">
                                <div class="menu-icon-container me-2" :class="isActive('admin/talleres') ? 'icon-active' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <span x-show="!collapsed || isMobile()">Talleres</span>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && auth()->user()->hasPermission('usuarios'))
                        <!-- Usuarios -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'usuarios') ? null : 'usuarios'"
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/usuarios') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/usuarios') ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Usuarios</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'usuarios' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeMenu === 'usuarios' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.usuarios.usuarios.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small position-relative"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/usuarios/usuarios') ? 'submenu-active' : ''
                                ]">
                                    Usuarios
                                </a>
                                <a href="{{ route('admin.usuarios.roles.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small position-relative"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/usuarios/roles') ? 'submenu-active' : ''
                                ]">
                                    Roles
                                </a>
                            </div>
                        </li>
                        @endif

                        @if(auth()->check() && auth()->user()->hasPermission('configuracion'))
                        <!-- Subir archivos de la empresa -->
                        <li class="nav-item">
                            <a href="{{ route('admin.archivos-empresa.index') }}"
                            class="nav-link d-flex align-items-center px-3 py-2 mb-1 rounded"
                            :class="[
                                darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                isActive('admin/archivos-empresa') ? 'menu-active' : ''
                            ]">
                                <div class="menu-icon-container me-2" :class="isActive('admin/archivos-empresa/index.blade.php') ? 'icon-active' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 .1M9 19l3 3m0 0l3-3m-3 3V10" />
                                    </svg>
                                </div>
                                <span x-show="!collapsed || isMobile()">Archivos de la empresa</span> 
                            </a>
                        </li>
                        
                        <!-- Configuración -->
                        <li class="nav-item">
                            <button @click="activeMenu = (activeMenu === 'configuracion') ? null : 'configuracion'"
                                    class="nav-link d-flex align-items-center justify-content-between w-100 px-3 py-2 mb-1 rounded text-start btn-no-border"
                                    :class="[
                                        darkMode ? 'text-light hover-dark' : 'text-dark hover-light',
                                        isActive('admin/configuracion') ? 'menu-active' : ''
                                    ]">
                                <div class="d-flex align-items-center">
                                    <div class="menu-icon-container me-2" :class="isActive('admin/configuracion') ? 'icon-active' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543 .94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543 .826-3.31 2.37-2.37 1 .608 2.296 .07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span x-show="!collapsed || isMobile()">Configuración</span>
                                </div>
                                <svg x-show="!collapsed || isMobile()" :class="{ 'rotate-180': activeMenu === 'configuracion' }" class="ms-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeMenu === 'configuracion' && (!collapsed || isMobile())" class="ms-4 my-1">
                                <a href="{{ route('admin.configuracion.maestros.fabricantes.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/maestros/fabricantes') ? 'submenu-active' : ''
                                ]">Fabricantes</a>
                                <a href="{{ route('admin.configuracion.maestros.estandar_mantenimiento.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/maestros/estandar_mantenimiento') ? 'submenu-active' : ''
                                ]">Estándares de Mantenimiento</a>
                                <a href="{{ route('admin.configuracion.centros_costos.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/centros_costos') ? 'submenu-active' : ''
                                ]">Centros de Costos</a>
                                <a href="{{ route('admin.configuracion.maestros.cargos.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/maestros/cargos') ? 'submenu-active' : ''
                                ]">Cargos</a>
                                <a href="{{ route('admin.configuracion.maestros.bancos.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/maestros/bancos') ? 'submenu-active' : ''
                                ]">Bancos</a>
                                <a href="{{ route('admin.configuracion.unidades.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/unidades') ? 'submenu-active' : ''
                                ]">Unidades</a>
                                <a href="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.index') }}" 
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/reglas-vencimiento-cotizaciones') ? 'submenu-active' : ''
                                ]">Reglas de Vencimiento</a>
                                <a href="{{ route('admin.configuracion.tipos-cambio.index') }}"
                                class="nav-link d-block py-2 px-3 rounded small"
                                :class="[
                                    darkMode ? 'text-light-50 hover-dark' : 'text-secondary hover-light',
                                    isActive('admin/configuracion/tipos-cambio') ? 'submenu-active' : ''
                                ]">
                                    <i class="fas fa-exchange-alt me-2"></i>Tipos de Cambio
                                    <small class="ms-2 d-inline-flex align-items-center">
                                        <span class="badge bg-success badge-notification rounded-pill">USD/PEN</span>
                                    </small>
                                </a>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>

                <!-- User Info -->
                <div class="border-top" :class="darkMode ? 'border-dark-custom' : ''">
                    <div class="p-2">
                        <!-- Componente con estado userDropdown -->
                        <div x-data="{ userDropdown: false }" class="position-relative">
                            <!-- Botón para alternar el dropdown -->
                            <button @click="userDropdown = !userDropdown" 
                                    class="btn btn-no-border d-flex align-items-center w-100 text-start p-2 rounded">
                                <img class="rounded-circle me-2" 
                                     style="height: 1.5rem; width: 1.5rem; object-fit: cover;" 
                                     src="{{ Auth::user()->profile_photo_url }}" 
                                     alt="{{ Auth::user()->name }}">
                                <div x-show="!collapsed || isMobile()" class="flex-grow-1 overflow-hidden">
                                    <p class="mb-0 small fw-medium text-truncate" 
                                       :class="darkMode ? 'text-light' : 'text-dark'">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="mb-0 small text-muted text-truncate">
                                        {{ Auth::user()->email }}
                                    </p>
                                </div>
                                <svg x-show="!collapsed || isMobile()" 
                                     xmlns="http://www.w3.org/2000/svg" 
                                     class="ms-2" 
                                     :class="{ 'rotate-180': userDropdown }" 
                                     style="height: 1.25rem; width: 1.25rem;" 
                                     fill="none" 
                                     viewBox="0 0 24 24" 
                                     stroke="currentColor">
                                    <path stroke-linecap="round" 
                                          stroke-linejoin="round" 
                                          stroke-width="2" 
                                          d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown del usuario -->
                            <div x-show="userDropdown" 
                                 @click.away="userDropdown = false" 
                                 class="position-absolute bottom-100 start-0 mb-2 bg-white rounded shadow-lg" 
                                 style="width: 14rem; z-index: 1080;" 
                                 :class="darkMode ? 'bg-dark-custom' : ''">
                                <!-- Cerrar sidebar para móviles -->
                                <button @click="sidebarOpen = false; userDropdown = false" 
                                        class="dropdown-item d-md-none d-flex align-items-center py-2 px-3" 
                                        :class="darkMode ? 'text-light hover-dark' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Cerrar Sidebar</span>
                                </button>

                                <!-- Colapsar/Expandir sidebar -->
                                <button @click="collapsed = !collapsed; userDropdown = false" 
                                        class="dropdown-item d-none d-md-flex align-items-center py-2 px-3" 
                                        :class="darkMode ? 'text-light hover-dark' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                    </svg>
                                    <span x-text="collapsed ? 'Expandir Sidebar' : 'Contraer Sidebar'"></span>
                                </button>

                                <div class="dropdown-divider my-1" :class="darkMode ? 'border-dark-custom' : ''"></div>

                                <!-- Enlace a perfil -->
                                <a href="{{ route('profile.show') }}" 
                                   class="dropdown-item d-flex align-items-center py-2 px-3" 
                                   :class="darkMode ? 'text-light hover-dark' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Mi Perfil</span>
                                </a>

                                <!-- Alternar modo oscuro -->
                                <button @click="darkMode = !darkMode" 
                                        class="dropdown-item d-flex align-items-center py-2 px-3" 
                                        :class="darkMode ? 'text-light hover-dark' : ''">
                                    <template x-if="darkMode">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </template>
                                    <template x-if="!darkMode">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                        </svg>
                                    </template>
                                    <span x-text="darkMode ? 'Modo Claro' : 'Modo Oscuro'"></span>
                                </button>

                                <div class="dropdown-divider mt-1" :class="darkMode ? 'border-dark-custom' : ''"></div>

                                <!-- Formulario de logout -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <button type="submit" @click.prevent="$root.submit();" 
                                            class="dropdown-item d-flex align-items-center py-2 px-3 text-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
           <div class="flex-grow-1 overflow-hidden main-content"
               :class="!isMobile() ? (collapsed ? 'ml-collapsed' : 'ml-expanded') : ''">
                    <style>
                        @media (min-width: 768px) {
                            .sidebar {
                                position: fixed;
                                left: 0;
                                top: 0;
                                bottom: 0;
                            }
                            .main-content.ml-expanded {
                                margin-left: 16rem !important;
                                width: calc(100% - 16rem);
                            }
                            .main-content.ml-collapsed {
                                margin-left: 5rem !important;
                                width: calc(100% - 5rem);
                            }
                        }
                        @media (max-width: 767.98px) {
                            .sidebar {
                                width: 16rem !important;
                            }
                            .main-content {
                                margin-left: 0 !important;
                                width: 100% !important;
                            }
                        }
                    </style>
                <style>
                    /* Sidebar ancho fijo en escritorio */
                    @media (min-width: 768px) {
                        .sidebar.sidebar-expanded {
                            width: 16rem !important;
                        }
                        .sidebar.sidebar-collapsed {
                            width: 5rem !important;
                        }
                        .main-content.ml-expanded {
                            margin-left: 16rem !important;
                        }
                        .main-content.ml-collapsed {
                            margin-left: 5rem !important;
                        }
                    }
                    @media (max-width: 767.98px) {
                        .sidebar {
                            width: 16rem !important;
                        }
                        .main-content {
                            margin-left: 0 !important;
                        }
                    }
                </style>
            <!-- Main Content -->
            <main class="bg-light overflow-auto w-100" style="min-height: 100vh;" :class="darkMode ? 'bg-dark' : 'bg-light'">
                <!-- Mobile header spacing -->
                <div class="d-md-none" style="height: 4rem;"></div>
                
                <!-- Content padding -->
                <div class="p-3 p-md-4">
                    @if (isset($header))
                        <div class="mb-4">{{ $header }}</div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts para funcionalidad del sidebar -->
    <script>
        // Script para mantener submenús abiertos según la ruta actual
        document.addEventListener('alpine:init', () => {
            Alpine.data('activeRouteTracker', () => ({
                init() {
                    // Configuración inicial para rutas y submenús
                    const currentPath = window.location.pathname;
                    
                    // Verificar si estamos en una ruta específica
                    this.isInRoute = (path) => {
                        return currentPath.includes(path);
                    };
                    
                    // Determinar qué menús deberían estar abiertos
                    this.shouldOpenMenu = (menuPaths) => {
                        return menuPaths.some(path => currentPath.includes(path));
                    };
                }
            }));
        });

        // Funcionalidad de navegación
        document.addEventListener('alpine:init', () => {
            Alpine.data('navigation', () => ({
                activeSubMenu: '',
                toggleSubMenu(menu) {
                    this.activeSubMenu = this.activeSubMenu === menu ? '' : menu;
                }
            }))
        });

        // Función global para el sidebar de comentarios (si es necesaria)
        window.abrirSidebarComentarios = function(seguimientoId) {
            console.log('Abriendo sidebar para seguimiento:', seguimientoId);
            
            // Si el sidebar no existe, creamos uno simple
            let sidebarComentarios = document.getElementById('sidebarComentarios');
            
            if (!sidebarComentarios) {
                // Crear un sidebar básico
                const sidebarHTML = `
                <div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarComentarios" aria-labelledby="sidebarComentariosLabel">
                    <div class="offcanvas-header border-bottom py-3">
                        <div>
                            <h5 class="offcanvas-title mb-0 d-flex align-items-center" id="sidebarComentariosLabel">
                                <i class="fas fa-comment-dots me-2"></i>
                                Comentarios del seguimiento #${seguimientoId}
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    
                    <div class="offcanvas-body d-flex flex-column p-0">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Cargando comentarios...</p>
                        </div>
                    </div>
                </div>`;
                
                // Añadir al DOM
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = sidebarHTML;
                document.body.appendChild(tempDiv.firstElementChild);
                
                // Obtener referencia
                sidebarComentarios = document.getElementById('sidebarComentarios');
            }
            
            // Almacenar ID en variable global
            window.seguimientoActual = seguimientoId;
            
            // Inicializar el offcanvas con Bootstrap
            try {
                const bsOffcanvas = new bootstrap.Offcanvas(sidebarComentarios);
                bsOffcanvas.show();
            } catch (error) {
                console.error('Error al inicializar sidebar:', error);
                alert('Error al mostrar la ventana de comentarios. Intente actualizar la página.');
            }
        };
    </script>

    @stack('modals')
    @livewireScripts
    
    <!-- Scripts de la aplicación con Vite -->
    @vite(['resources/js/app.js'])
    
    @stack('scripts')
</body>
</html>