<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of brands
     */
    public function index()
    {
        $brands = Brand::withCount('cars')
                      ->orderBy('name')
                      ->paginate(20);

        return view('pages.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand
     */
    public function create()
    {
        return view('pages.brands.create');
    }

    /**
     * Store a newly created brand
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['name', 'description']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
            $data['logo'] = $logoPath;
        }

        Brand::create($data);

        return redirect()->route('brands.index')
                        ->with('success', 'Марката беше създадена успешно!');
    }

    /**
     * Display the specified brand and its cars
     */
    public function show(Brand $brand, Request $request)
    {
        $query = $brand->cars()->with(['category', 'user']);

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search within brand's cars
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['created_at', 'price', 'name'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $cars = $query->paginate(12);
        
        // Get categories for filter dropdown
        $categories = \App\Models\Category::whereHas('cars', function($q) use ($brand) {
            $q->where('brand_id', $brand->id);
        })->get();

        // Get price range for this brand
        $priceStats = $brand->cars()->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        return view('pages.brands.show', compact('brand', 'cars', 'categories', 'priceStats'));
    }

    /**
     * Show the form for editing the specified brand
     */
    public function edit(Brand $brand)
    {
        return view('pages.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['name', 'description']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo) {
                \Storage::disk('public')->delete($brand->logo);
            }
            
            $logoPath = $request->file('logo')->store('brands', 'public');
            $data['logo'] = $logoPath;
        }

        $brand->update($data);

        return redirect()->route('brands.show', $brand)
                        ->with('success', 'Марката беше обновена успешно!');
    }

    /**
     * Remove the specified brand
     */
    public function destroy(Brand $brand)
    {
        // Check if brand has cars
        if ($brand->cars()->count() > 0) {
            return redirect()->route('brands.index')
                            ->with('error', 'Не можете да изтриете марка, която има автомобили!');
        }

        // Delete logo if exists
        if ($brand->logo) {
            \Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('brands.index')
                        ->with('success', 'Марката беше изтрита успешно!');
    }

    /**
     * Get popular brands (for homepage or sidebar)
     */
    public function popular()
    {
        $brands = Brand::withCount('cars')
                      ->having('cars_count', '>', 0)
                      ->orderBy('cars_count', 'desc')
                      ->limit(10)
                      ->get();

        return view('components.popular-brands', compact('brands'));
    }

    /**
     * API endpoint for getting brands (for AJAX calls)
     */
    public function api()
    {
        $brands = Brand::select('id', 'name')
                      ->orderBy('name')
                      ->get();

        return response()->json($brands);
    }
}