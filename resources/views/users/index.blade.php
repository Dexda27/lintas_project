@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0 mb-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">User Management</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 sm:px-4 rounded-lg inline-flex items-center justify-center text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="hidden sm:inline">Add User</span>
            <span class="sm:hidden">Add</span>
        </a>
    </div>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('users.index') }}" class="bg-white p-3 sm:p-4 rounded-lg shadow mb-4 sm:mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="search" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Name, email, or region..."
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="role" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin_region" {{ request('role') == 'admin_region' ? 'selected' : '' }}>Regional Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div>
                <label for="region" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Region</label>
                <select id="region" name="region" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-1 flex items-end gap-2">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-3 sm:px-4 py-2 rounded-md text-sm flex-1 lg:flex-initial">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
                <a href="{{ route('users.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-3 sm:px-4 py-2 rounded-md text-sm flex-1 lg:flex-initial text-center">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <!-- Mobile Card View (visible on screens smaller than lg) -->
    <div class="lg:hidden">
        @forelse($users as $user)
        <div class="border-b border-gray-200 p-4 last:border-b-0">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                        <span class="text-white font-medium text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($user->role == 'super_admin')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            Super Admin
                        </span>
                        @elseif($user->role == 'admin_region')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            Regional Admin
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            User
                        </span>
                        @endif

                        @if(isset($user->is_active) && !$user->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Inactive
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <div>
                            <span class="font-medium">Region:</span> {{ $user->region ?? '-' }}
                        </div>
                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-3">
                        <a href="{{ route('users.show', $user) }}" class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo hover:bg-indigo-100 border border-indigo-200 rounded-full transition-colors duration-150">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow hover:bg-yellow-100 border border-yellow-200 rounded-full transition-colors duration-150">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>

                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center justify-center w-8 h-8 hover:bg-red-100 border border-red-200 rounded-full transition-colors duration-150">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 sm:p-12 text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-8 w-8 sm:h-12 sm:w-12 text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <p class="text-sm sm:text-lg font-medium">No users found</p>
                <p class="mt-1 text-xs sm:text-base">Try adjusting your search filters</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View (visible on lg screens and up) -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th> -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->role == 'super_admin')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            Super Admin
                        </span>
                        @elseif($user->role == 'admin_region')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Regional Admin
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            User
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $user->region ?? '-' }}
                    </td>
                    <!-- <td class="px-6 py-4 whitespace-nowrap">
                        @if(isset($user->is_active) && !$user->is_active)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                            Inactive
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                            Active
                        </span>
                        @endif
                    </td> -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                                <i data-lucide="eye" class="w-4 h-4"></i> View
                            </a>

                            <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900 flex items-center gap-1">
                                <i data-lucide="edit" class="w-4 h-4"></i> Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center gap-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <p class="text-lg font-medium">No users found</p>
                            <p class="mt-1">Try adjusting your search filters</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Responsive Pagination Section -->
    @if($users->hasPages())
    <div class="px-3 sm:px-6 py-3 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <!-- Showing Results Info -->
            <div class="text-center sm:text-left">
                <p class="text-xs sm:text-sm text-gray-700">
                    <span class="hidden sm:inline">Showing</span>
                    <span class="font-medium">{{ $users->firstItem() ?: 0 }}</span>
                    <span class="hidden sm:inline">to</span>
                    <span class="sm:hidden">-</span>
                    <span class="font-medium">{{ $users->lastItem() ?: 0 }}</span>
                    <span class="hidden sm:inline">of</span>
                    <span class="sm:hidden">/</span>
                    <span class="font-medium">{{ $users->total() }}</span>
                    <span class="hidden sm:inline">results</span>
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex justify-center sm:justify-end">
                <div class="flex items-center space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($users->onFirstPage())
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-l-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    @else
                    <a href="{{ $users->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    @endif

                    {{-- Mobile: Show only current page and total pages --}}
                    <div class="sm:hidden flex items-center">
                        <span class="relative inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-blue-600">
                            {{ $users->currentPage() }} / {{ $users->lastPage() }}
                        </span>
                    </div>

                    {{-- Desktop: Show page numbers --}}
                    <div class="hidden sm:flex items-center space-x-1">
                        @php
                        $start = max(1, $users->currentPage() - 2);
                        $end = min($users->lastPage(), $users->currentPage() + 2);
                        @endphp

                        @if($start > 1)
                        <a href="{{ $users->appends(request()->query())->url(1) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            1
                        </a>
                        @if($start > 2)
                        <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">
                            ...
                        </span>
                        @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if ($page==$users->currentPage())
                            <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $users->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                {{ $page }}
                            </a>
                            @endif
                            @endfor

                            @if($end < $users->lastPage())
                                @if($end < $users->lastPage() - 1)
                                    <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">
                                        ...
                                    </span>
                                    @endif
                                    <a href="{{ $users->appends(request()->query())->url($users->lastPage()) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                        {{ $users->lastPage() }}
                                    </a>
                                    @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if ($users->hasMorePages())
                    <a href="{{ $users->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    @else
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    /* Responsive touch targets */
    @media (max-width: 640px) {

        button,
        a,
        select,
        input {
            min-height: 44px;
        }

        /* Better spacing for mobile cards */
        .space-x-3>*+* {
            margin-left: 0.75rem;
        }

        /* Ensure truncation works properly */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    /* Hover effects only on desktop */
    @media (min-width: 1024px) {
        .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
        }

        .hover\:text-indigo-900:hover {
            color: #312e81;
        }

        .hover\:text-yellow-900:hover {
            color: #78350f;
        }

        .hover\:text-red-900:hover {
            color: #7f1d1d;
        }

        .hover\:bg-blue-700:hover {
            background-color: #1d4ed8;
        }

        .hover\:bg-gray-700:hover {
            background-color: #374151;
        }

        .hover\:bg-gray-500:hover {
            background-color: #6b7280;
        }
    }

    /* Focus styles for accessibility */
    input:focus,
    select:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }

    /* Smooth transitions */
    .transition-colors {
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Better visual hierarchy for mobile */
    @media (max-width: 640px) {
        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }
    }

    /* Tablet optimizations */
    @media (min-width: 641px) and (max-width: 1023px) {
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush
@endsection