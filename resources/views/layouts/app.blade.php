<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <title>@yield('title', 'Fiber Core Management')</title>

    </script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-800 shadow-lg">
            <div class=" mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-white text-xl font-bold">Fiber Core Management</h1>
                    </div>
                    <!-- Hamburger for mobile -->
                    <div class="md:hidden flex items-center">
                        <button id="sidebar-toggle" class="text-white focus:outline-none">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- ...user info & logout... -->
                        <div class="text-white">
                            <span class="text-sm">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->isAdminRegion())
                            <span class="text-xs bg-blue-600 px-2 py-1 rounded ml-2">{{ auth()->user()->region }}</span>
                            @elseif(auth()->user()->isSuperAdmin())
                            <span class="text-xs bg-purple-600 px-2 py-1 rounded ml-2">Super Admin</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Responsive Sidebar -->
        <div class="flex">
            <!-- Sidebar -->
            <div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-md min-h-screen flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200 md:static md:inset-auto md:z-auto md:flex md:w-64">
                <div class="p-4 flex-1">
                    <ul class="space-y-2">
                        <li>
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
                        <!-- Divider for admin sections -->
                        <li class="border-t border-gray-200 my-4"></li>
                        <li>
                            <div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Administration
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}" class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i data-lucide="Users" class="w-5 h-5 mr-3"></i>
                                User Management
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                <!-- Logout button at bottom -->
                <div class="p-4 mt-auto">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="flex items-center w-full p-2 text-gray-700 rounded hover:bg-red-50 hover:text-red-700 transition-colors">
                            <i data-lucide="Log-Out" class="w-5 h-5 mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            <!-- Overlay for mobile sidebar -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden"></div>
            <!-- Main Content -->
            <div class="flex-1 p-4 md:p-8">
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

    <!-- Modal Container -->
    <div id="modal-container"></div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-96 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Confirm Logout</h3>
            </div>
            <p class="text-sm text-gray-500 mb-6">
                Are you sure you want to logout? You will need to sign in again to access your account.
            </p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeLogoutModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" onclick="performLogout()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                    Yes, Logout
                </button>
            </div>
        </div>
    </div>

    <script>
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

        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        sidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay?.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>

</body>
@stack('scripts')

</html>
