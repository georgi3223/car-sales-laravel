<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Car Dealership')</title>
</head>
<body>
    <div style="display: flex; min-height: 100vh;">
        <!-- Sidebar -->
        <aside style="width: 250px; background: #2c3e50; color: white; padding: 20px;">
            <div>
                <h2>Admin Panel</h2>
                <hr>
            </div>
            
            <nav>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin: 10px 0;">
                        <a href="{{ route('admin.dashboard') }}" style="color: white; text-decoration: none;">
                            📊 Dashboard
                        </a>
                    </li>
                    <li style="margin: 10px 0;">
                        <a href="{{ route('admin.cars.index') }}" style="color: white; text-decoration: none;">
                            🚗 Manage Cars
                        </a>
                    </li>
                    <li style="margin: 10px 0;">
                        <a href="{{ route('brands.index') }}" style="color: white; text-decoration: none;">
                            🏢 Manage Brands
                        </a>
                    </li>
                    <li style="margin: 10px 0;">
                        <a href="{{ route('categories.index') }}" style="color: white; text-decoration: none;">
                            📂 Manage Categories
                        </a>
                    </li>
                    <li style="margin: 10px 0;">
                        <a href="{{ route('contacts.index') }}" style="color: white; text-decoration: none;">
                            📧 Contact Messages
                        </a>
                    </li>
                    <hr>
                    <li style="margin: 10px 0;">
                        <a href="{{ route('home') }}" style="color: white; text-decoration: none;">
                            🏠 View Site
                        </a>
                    </li>
                    <li style="margin: 10px 0;">
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">
                                🚪 Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main style="flex: 1; padding: 20px; background: #ecf0f1;">
            <!-- Header -->
            <header style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; justify-content: between; align-items: center;">
                    <div>
                        <h1 style="margin: 0;">@yield('page-title', 'Admin Dashboard')</h1>
                        <p style="margin: 5px 0 0 0; color: #666;">Welcome back, {{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <span>{{ date('F j, Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Messages -->
            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page Content -->
            <div style="background: white; padding: 20px; border-radius: 5px;">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>