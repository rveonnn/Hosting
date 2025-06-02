<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\View\View;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Yajra\DataTables\DataTables;



class ProductController extends Controller
{
    public function index() : View
    {
        $products = Product::latest()->paginate(5);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:6000',
            'title'         => 'required|min:5',
            'description'   => 'required|min:5',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        $image = $request->file('image');
        $image->storeAs('products', $image->hashName());

        Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'description'   => $request->description,
            'price'         => $request->price,
            'stock'         => $request->stock
        ]);
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        // Kalau permintaan datang dari AJAX / expects JSON
        if (request()->ajax()) {
            return response()->json($product);
        }

        // Kalau bukan dari AJAX, kembalikan ke tampilan biasa
        return view('products.show', compact('product'));
    }

    public function edit(string $id): View
    {
        $product = Product::findOrFail($id);

        return view('products.edit',  compact('product'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);
        $product = Product::findOrFail($id);

        if($request->hasFile('image')) {
            Storage::delete('products/' . $products->image);
            $product->update([
                'image'         => $image->hashName(),
                'title'         => $request->title,
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock
            ]);
        } else {
            $product->update([
                'title'         => $request->title,
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock
            ]);
        }
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah']);
    }

    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        Storage::delete('products/',$product->image);

        $product->delete();

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus']);
    }

    public function json()
    {

        return response()->json(Product::all());
    }

    public function ajax()
    {
        $products = Product::select(['id','title','price','stock','image']);

        return DataTables::of($products)
            ->addColumn('image', function ($product){
                return '<img src="' . asset('storage/product/' . $products->image) . '" class="rounded" width="100">';
            })
            ->addColumn('action', function ($product){
                return '
                <a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-dark">Show</a>
                <a href="' . route('products.edit', $product->id) . '" class="btn btn-sm btn-primary">Edit</a>
                <button data-id="' . $product->id . '" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                ';

            })
            ->editColumn('price', function ($product){
                return 'Rp ' . number_format($product->price, 2, ',', '.');
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }
}
