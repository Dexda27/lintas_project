<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fiber Core Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-screen flex">
    <!-- Left Side -->
    <div class="hidden lg:flex w-1/2 bg-gray-900 relative">
        <img src="{{ asset('assets/images/login.jpg') }}" 
             alt="Login Background" 
             class="object-cover w-full h-full opacity-80">
        
        <!-- Overlay Content -->
        <div class="absolute bottom-10 left-10 text-white">
            <p class="text-sm"></p>
            <p class="text-sm"></p>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex w-full lg:w-1/2 items-center justify-center bg-white">
        <div class="max-w-md w-full p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <img src="{{ asset('assets/images/logo.png') }}" 
                    alt="Logo" 
                    class="mx-auto w-50 h-30">
               
            </div>
             <div class="text-center mb-8">
                
                <span class="text-2xl font-bold text-gray-900 mt-0">Fiber Core Management</span>
                <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
            
                <div id="flash-success"
                class="  bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="flash-error"
                class=" bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            value="{{ old('email') }}"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                            placeholder="your@email.com"
                        />
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
                        />
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

               

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                >
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Login
                </button>
            </form>

            <!-- Demo Accounts -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Demo Accounts:</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Superadmin:</strong> superadmin@fiber.com / password</div>
                    <div><strong>Regional (Bali):</strong> bali@fiber.com / password</div>
                    <div><strong>Regional (NTB):</strong> NTT@fiber.com / password</div>
                </div>
            </div>

            <!-- Register Link (only show if no users exist) -->
            @if(\App\Models\User::count() == 0)
                <div class="text-center mt-6">
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Buat akun pertama (Setup awal)
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        setTimeout(() => {
        const success = document.getElementById('flash-success');
        const error = document.getElementById('flash-error');
        
        if (success) {
            success.classList.add('opacity-0'); // fade out
            setTimeout(() => success.remove(), 500); // hapus dari DOM
        }

        if (error) {
            error.classList.add('opacity-0');
            setTimeout(() => error.remove(), 500);
        }
    }, 3000); // 3 detik
    </script>
</body>
</html>
