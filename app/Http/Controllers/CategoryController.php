<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::forUser(Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_default'] = false;
        $validated['icon'] = $validated['icon'] ?? 'circle';

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->is_default) {
            return redirect()->route('categories.index')->with('error', 'Kategori bawaan tidak bisa diubah.');
        }
        if ($category->user_id != Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->is_default) {
            return redirect()->route('categories.index')->with('error', 'Kategori bawaan tidak bisa dihapus.');
        }
        if ($category->user_id != Auth::id()) abort(403);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
