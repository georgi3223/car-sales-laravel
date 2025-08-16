@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div>
    <!-- Statistics Cards -->
    <div>
        <div>
            <h3>{{ $totalCars }}</h3>
            <p>Total Cars</p>
            <small>{{ $carsThisMonth }} added this month</small>
        </div>
        
        <div>
            <h3>{{ $totalBrands }}</h3>
            <p>Total Brands</p>
            <small>{{ $brandsThisMonth }} added this month</small>
        </div>
        
        <div>
            <h3>{{ $totalCategories }}</h3>
            <p>Total Categories</p>
            <small>Active categories</small>
        </div>
        
        <div>
            <h3>{{ $totalContacts }}</h3>
            <p>Contact Messages</p>
            <small>{{ $unreadContacts }} unread</small>
        </div>
    </div>

    <!-- Recent Activity -->
    <div>
        <div>
            <h2>Recent Cars Added</h2>
            @if($recentCars->count() > 0)
                <table border="1">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCars as $car)
                            <tr>
                                <td>
                                    @if($car->image)
                                        <img src="{{ asset('storage/cars/' . $car->image) }}" alt="{{ $car->title }}" width="50">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>{{ $car->title }}</td>
                                <td>{{ $car->brand->name }}</td>
                                <td>${{ number_format($car->price) }}</td>
                                <td>{{ $car->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('cars.show', $car->id) }}">View</a>
                                    <a href="{{ route('cars.edit', $car->id) }}">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p><a href="{{ route('admin.cars.index') }}">View all cars →</a></p>
            @else
                <p>No cars added yet.</p>
            @endif
        </div>

        <div>
            <h2>Recent Contact Messages</h2>
            @if($recentContacts->count() > 0)
                <table border="1">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentContacts as $contact)
                            <tr>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ Str::limit($contact->subject, 30) }}</td>
                                <td>{{ $contact->created_at->format('M j, Y') }}</td>
                                <td>{{ $contact->is_read ? 'Read' : 'Unread' }}</td>
                                <td>
                                    <a href="{{ route('contacts.show', $contact->id) }}">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p><a href="{{ route('contacts.index') }}">View all messages →</a></p>
            @else
                <p>No contact messages yet.</p>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <h2>Quick Actions</h2>
        <div>
            <a href="{{ route('cars.create') }}">Add New Car</a>
            <a href="{{ route('brands.create') }}">Add New Brand</a>
            <a href="{{ route('categories.create') }}">Add New Category</a>
            <a href="{{ route('contacts.index') }}">View Messages</a>
        </div>
    </div>

    <!-- Popular Brands Chart -->
    <div>
        <h2>Most Popular Brands</h2>
        @if($popularBrands->count() > 0)
            <table border="1">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Cars</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($popularBrands as $brand)
                        <tr>
                            <td>{{ $brand->name }}</td>
                            <td>{{ $brand->cars_count }}</td>
                            <td>{{ round(($brand->cars_count / $totalCars) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection