<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
use App\Models\Category;
use App\Http\Requests\CarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of cars
     */
    public function index(Request $request)
    {
        $query = Car::with(['brand', 'category']);

        // Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $cars = $query->orderBy('created_at', 'desc')->paginate(12);
        $brands = Brand::all();
        $categories = Category::all();

        return view('pages.cars.index', compact('cars', 'brands', 'categories'));
    }

    /**
     * Show the form for creating a new car
     */
    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();

        return view('pages.cars.create', compact('brands', 'categories'));
    }

    /**
     * Store a newly created car
     */
    public function store(CarRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cars', 'public');
            $data['image'] = $imagePath;
        }

        $data['user_id'] = auth()->id();

        Car::create($data);

        return redirect()->route('cars.index')
                        ->with('success', 'Автомобилът беше създаден успешно!');
    }

    /**
     * Display the specified car
     */
    public function show(Car $car)
    {
        $car->load(['brand', 'category', 'user']);
        
        // Get related cars (same brand or category)
        $relatedCars = Car::where('id', '!=', $car->id)
                         ->where(function($query) use ($car) {
                             $query->where('brand_id', $car->brand_id)
                                   ->orWhere('category_id', $car->category_id);
                         })
                         ->with(['brand', 'category'])
                         ->limit(4)
                         ->get();

        return view('pages.cars.show', compact('car', 'relatedCars'));
    }

    /**
     * Show the form for editing the specified car
     */
    public function edit(Car $car)
    {
        // Check if user owns the car or is admin
        if ($car->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $brands = Brand::all();
        $categories = Category::all();

        return view('pages.cars.edit', compact('car', 'brands', 'categories'));
    }

    /**
     * Update the specified car
     */
    public function update(CarRequest $request, Car $car)
    {
        // Check if user owns the car or is admin
        if ($car->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }
            
            $imagePath = $request->file('image')->store('cars', 'public');
            $data['image'] = $imagePath;
        }

        $car->update($data);

        return redirect()->route('cars.show', $car)
                        ->with('success', 'Автомобилът беше обновен успешно!');
    }

    /**
     * Remove the specified car
     */
    public function destroy(Car $car)
    {
        // Check if user owns the car or is admin
        if ($car->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // Delete image if exists
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return redirect()->route('cars.index')
                        ->with('success', 'Автомобилът беше изтрит успешно!');
    }

    /**
     * Display user's cars
     */
    public function myCars()
    {
        $cars = Car::where('user_id', auth()->id())
                   ->with(['brand', 'category'])
                   ->orderBy('created_at', 'desc')
                   ->paginate(10);

        return view('pages.cars.my-cars', compact('cars'));
    }
}