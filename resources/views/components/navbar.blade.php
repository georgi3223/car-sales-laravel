<nav>
    <div>
        <!-- Logo/Brand -->
        <div>
            <a href="{{ route('home') }}">CarDealer</a>
    </nav>

        <!-- Main Navigation -->
        <div>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('cars.index') }}">Cars</a>
            <a href="{{ route('brands.index') }}">Brands</a>
            <a href="{{ route('categories.index') }}">Categories</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('about') }}">About</a>
        </div>

        <!-- User Navigation -->
        <div>
            @guest
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @else
                <span>Welcome, {{ auth()->user()->name }}</span>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @endguest
        </div>
    </div>