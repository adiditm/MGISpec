<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PromoBanner;
use App\Models\Slider;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'sliders' => Slider::active()->get(),
            'promos' => PromoBanner::active()->get(),
            'products' => Product::active()->where('is_featured', true)->with('category')->get(),
        ]);
    }
}
