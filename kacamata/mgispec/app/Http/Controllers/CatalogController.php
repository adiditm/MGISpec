<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::active()->withCount(['products' => fn ($q) => $q->where('is_active', true)])->get();
        $current = $categories->firstWhere('slug', $request->query('kategori'));

        $products = Product::active()
            ->with('category')
            ->when($current, fn ($q) => $q->where('category_id', $current->id))
            ->get();

        return view('catalog.index', compact('categories', 'current', 'products'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $related = Product::where('is_active', true)
            ->with('category')
            ->whereKeyNot($product->getKey())
            ->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$product->category_id])
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        return view('catalog.show', compact('product', 'related'));
    }
}
