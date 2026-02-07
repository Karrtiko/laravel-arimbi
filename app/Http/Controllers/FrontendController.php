<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $settings = \App\Models\GeneralSetting::allSettings();
        $productsCount = (int) ($settings['products_home_count'] ?? 8);
        $bundlesCount = (int) ($settings['bundles_home_count'] ?? 3);

        $categories = Category::all();
        $countries = Country::all();
        $products = Product::with(['category', 'country', 'media'])
            ->where('is_active', true)
            ->latest()
            ->take($productsCount)
            ->get();
        $bundles = Bundle::with(['media', 'bundleProducts.product'])
            ->where('is_active', true)
            ->take($bundlesCount)
            ->get();

        $heroTitle = $settings['home_hero_title'] ?? 'Jajanan yang Kamu Kangenin, Skincare yang Kamu Butuhin.';
        $heroSubtitle = $settings['home_hero_subtitle'] ?? 'Pengen snack enak-enak hits dari luar negeri...';
        $heroImage = $settings['home_hero_image'] ?? null;

        return view('frontend.home', compact('categories', 'countries', 'products', 'bundles', 'heroTitle', 'heroSubtitle', 'heroImage'));
    }

    public function shop(Request $request)
    {
        $settings = \App\Models\GeneralSetting::allSettings();
        $perPage = (int) ($settings['products_per_page'] ?? 12);

        $categories = Category::all();
        $countries = Country::all();

        $query = Product::with(['category', 'country', 'media'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->whereHas('country', fn($q) => $q->where('slug', $request->country));
        }

        // Search
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->latest()->paginate($perPage);

        return view('frontend.shop', compact('categories', 'countries', 'products'));
    }

    public function about()
    {
        $settings = \App\Models\GeneralSetting::allSettings();
        $heroImage = $settings['about_hero_image'] ?? null;

        return view('frontend.about', compact('heroImage'));
    }

    public function product($slug)
    {
        $settings = \App\Models\GeneralSetting::allSettings();
        $relatedCount = (int) ($settings['related_products_count'] ?? 4);

        $product = Product::with(['category', 'country', 'media'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'country', 'media'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->take($relatedCount)
            ->get();

        return view('frontend.product', compact('product', 'relatedProducts'));
    }

    public function package($slug)
    {
        $settings = \App\Models\GeneralSetting::allSettings();
        // Reuse bundle home count or related count, usually packages don't have separate setting for related
        // But we can reuse related_products_count for simplicity or default to 3
        $otherCount = (int) ($settings['bundles_home_count'] ?? 3);

        $bundle = Bundle::with(['media', 'bundleProducts.product.media'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $otherBundles = Bundle::with(['media'])
            ->where('is_active', true)
            ->where('id', '!=', $bundle->id)
            ->take($otherCount)
            ->get();

        return view('frontend.package', compact('bundle', 'otherBundles'));
    }
}
