<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo2.png') }}">
    <title>@yield('title', 'Fiber Core Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Select2 CSS for enhanced selects used in CVLAN views -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
</head>

<body class="bg-gray-50">
    <!-- Flash Container -->
    <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-4"></div>

    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 shadow-lg fixed top-0 left-0 right-0 z-30 transition-all duration-300" id="navbar">

            <div class=" mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Hamburger Menu Button -->
                        <button id="sidebar-toggle" class="text-white focus:outline-none  p-2 rounded-md hover:bg-blue-700 transition-colors">
                            <i data-lucide="menu" class="w-6 h-6" id="hamburger-icon"></i>

                        </button>
                        <h4 class="text-md font-bold text-white tracking-wide">Fiber Core Management</h4>


                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- User info -->
                        <div class="text-yellow-600">
                            @if(auth()->user()->isAdminRegion())
                            <span class="flex items-center text-xs bg-white px-2 py-1 rounded">
                                <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                {{ auth()->user()->region }}
                            </span>
                            @elseif(auth()->user()->isSuperAdmin())
                            <span class="flex items-center text-xs bg-white px-2 py-1 rounded">
                                <i data-lucide="crown" class="w-4 h-4 mr-1 text-yellow-500"></i>
                                Super Admin
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Layout Container -->
        <div class="flex pt-16">
            <!-- Sidebar -->
            <div id="sidebar" class="fixed left-0 top-16 bottom-0 w-64 bg-white shadow-lg z-20 transform transition-transform duration-300 ease-in-out">
                <div class="h-full flex flex-col">
                    <div class="p-4 flex-1 overflow-y-auto">
                        <div class="mb-6">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-30 h-10 mx-auto">

                        </div>
                        <ul class="border-t border-gray-200 my-4"></ul>
                        <ul class="space-y-2">
                            <li>
                                <div class="px-2 py-1 mb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Fiber Optik Management
                                </div>
                                <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                                    Dashboard
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('cables.index') }}" class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs('cables.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <i data-lucide="Cable" class="w-5 h-5 mr-3"></i>
                                    Fiber Cores
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('closures.index') }}" class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs('closures.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <i data-lucide="Split" class="w-5 h-5 mr-3"></i>
                                    Joint Closures
                                </a>
                            </li>

                            @if(auth()->user()->isSuperAdmin())
                             <li class="border-t border-gray-200 my-4"></li>
                                <li>
                                    <div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        vlan Management
                                    </div>
                                    <a href="{{ route('svlan.index') }}" class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 ">
                                        <i data-lucide="network" class="w-5 h-5 mr-3"></i>
                                        Vlan Management
                                    </a>
                                </li>
                            @endif


                            @if(auth()->user()->isSuperAdmin())
                            <!-- Divider for admin sections -->
                            <li class="border-t border-gray-200 my-4"></li>
                            <li>
                                <div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Administration
                                </div>
                            </li>
                            <li>
                                <a href="{{ route('users.index') }}" class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <i data-lucide="User" class="w-5 h-5 mr-3"></i>
                                    User Management
                                </a>
                            </li>
                            @endif

                        </ul>
                    </div>



                    <!-- Logout button at bottom -->
                    <div class="p-4 mt-auto border-t border-gray-200">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button" onclick="confirmLogout()" class="flex items-center w-full p-2 text-gray-700 rounded hover:bg-red-50 hover:text-red-700 transition-colors cursor-pointer">
                                <i data-lucide="Log-Out" class="w-5 h-5 mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="sidebar-overlay" class="fixed inset-0 backdrop-blur-xs z-10 hidden transition-all duration-300"></div> <!-- Main Content -->
            <div id="main-content" class="flex-1 transition-all duration-300 ease-in-out ml-64">
                <div class="p-4 md:p-8">
                    @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                    @endif

                    @yield('content')


                </div>
            </div>
        </div>
    </div>

    <!-- Modal Container -->
    <div id="modal-container"></div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-xs z-50 hidden">
        <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-lg p-6 w-96 max-w-md mx-4 shadow-xl border border-white border-opacity-20">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 bg-opacity-80 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Confirm Logout</h3>
            </div>
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to logout? You will need to sign in again to access your account.
            </p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeLogoutModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white bg-opacity-80 backdrop-blur-sm border border-gray-300 border-opacity-50 rounded-md hover:bg-opacity-90 hover:bg-gray-50 transition-all duration-200">
                    Cancel
                </button>
                <button type="button" onclick="performLogout()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 bg-opacity-90 backdrop-blur-sm border border-transparent rounded-md hover:bg-opacity-100 hover:bg-red-700 transition-all duration-200">
                    Yes, Logout
                </button>
            </div>
        </div>
    </div>
    <footer class="mt-10 bg-white shadow-inner border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-center text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} Fiber Core Management. All rights reserved.</p>
        </div>
    </footer>
    </div>
    
    <script>
        // Sidebar state management
        let sidebarOpen = window.innerWidth >= 1024; // desktop terbuka, mobile/tablet tertutup

        // DOM elements
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mainContent = document.getElementById('main-content');
        const hamburgerIcon = document.getElementById('hamburger-icon');

        // Toggle sidebar function
        function toggleSidebar() {
            sidebarOpen = !sidebarOpen;

            if (sidebarOpen) {
                // Open sidebar
                sidebar.classList.remove('-translate-x-full');
                if (window.innerWidth >= 768) {
                    mainContent.classList.add('ml-64');
                    mainContent.classList.remove('ml-0');
                }
                hamburgerIcon.setAttribute('data-lucide', 'x');

                // Show overlay on mobile
                if (window.innerWidth < 768) {
                    sidebarOverlay.classList.remove('hidden');
                }
            } else {
                // Close sidebar
                sidebar.classList.add('-translate-x-full');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-0');
                hamburgerIcon.setAttribute('data-lucide', 'menu');

                // Hide overlay
                sidebarOverlay.classList.add('hidden');
            }

            // Recreate icons after changing the icon
            lucide.createIcons();
        }

        // Event listeners
        sidebarToggle.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking overlay (mobile)
        sidebarOverlay.addEventListener('click', function() {
            if (window.innerWidth < 768 && sidebarOpen) {
                toggleSidebar();
            }
        });

        // Close sidebar when clicking menu links on mobile
        function setupMobileSidebarClose() {
            const sidebarLinks = document.querySelectorAll('#sidebar a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Only auto-close on mobile when sidebar is open
                    if (window.innerWidth < 768 && sidebarOpen) {
                        // Small delay to allow navigation to start
                        setTimeout(() => {
                            toggleSidebar();
                        }, 100);
                    }
                });
            });
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                // Desktop/tablet view
                sidebarOverlay.classList.add('hidden');
                if (sidebarOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    mainContent.classList.add('ml-64');
                    mainContent.classList.remove('ml-0');
                } else {
                    // If closed, open it for desktop
                    sidebarOpen = true;
                    sidebar.classList.remove('-translate-x-full');
                    mainContent.classList.add('ml-64');
                    mainContent.classList.remove('ml-0');
                    hamburgerIcon.setAttribute('data-lucide', 'menu');
                    lucide.createIcons();
                }
            } else {
                // Mobile view
                if (sidebarOpen) {
                    sidebarOverlay.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    mainContent.classList.remove('ml-64');
                    mainContent.classList.add('ml-0');
                    sidebarOverlay.classList.add('hidden');
                }
            }
        });

        // Logout modal functions
        function confirmLogout() {
            document.getElementById('logout-modal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logout-modal').classList.add('hidden');
        }

        function performLogout() {
            document.getElementById('logout-form').submit();
        }

        // Close modal when clicking outside
        document.getElementById('logout-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('logout-modal').classList.contains('hidden')) {
                closeLogoutModal();
            }
        });

        // Initialize when DOM is loaded
        document.addEventListener("DOMContentLoaded", function() {
            // Set initial state based on screen size
            if (window.innerWidth < 768) {
                // Mobile: sidebar closed by default
                sidebarOpen = false;
                sidebar.classList.add('-translate-x-full');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-0');
                hamburgerIcon.setAttribute('data-lucide', 'menu');
            } else {
                // Desktop: sidebar open by default
                sidebarOpen = true;
                sidebar.classList.remove('-translate-x-full');
                mainContent.classList.add('ml-64');
                mainContent.classList.remove('ml-0');
                hamburgerIcon.setAttribute('data-lucide', 'menu');
            }

            // Setup mobile sidebar auto-close
            setupMobileSidebarClose();

            // Initialize icons
            lucide.createIcons();
        });
    </script>

    <!-- jQuery and Select2 (needed by CVLAN edit/create views) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')
</body>

</html>