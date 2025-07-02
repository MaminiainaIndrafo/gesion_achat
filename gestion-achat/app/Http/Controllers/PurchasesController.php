<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Purchases;
use App\Models\Stocks;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function sync()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function importForm()
    {
        // Récupérer les fournisseurs pour le formulaire d'importation
        $suppliers = Supplier::all();
        return view('purchases.import', [
        'suppliers' => Supplier::withCount('products')->get(),
        'products' => Products::with(['supplier', 'stock'])->paginate(10),
        'productsCount' => Products::count(),
        'suppliersCount' => Supplier::count(),
        'averageStock' => Stocks::average('quantity'),
        'averageMargin' => Products::average('margin_percent'),
        'lastSync' => Cache::get('last_api_sync')
    ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'supplier_id' => 'required|exists:suppliers,supplier_id'
        ]);

        $uploadedFile = $request->file('csv_file');
        $filename = uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->storeAs('temp', $filename);

        $fullPath = storage_path("app/private/{$path}");
        $path = str_replace("\\", "/", $fullPath);

        Artisan::call('app:import-purchases', [
            'file' => $path,
            '--supplier_id' => $request->supplier_id
        ]);

        $output = Artisan::output();

        // nettoyage
        Storage::delete($path);

        return back()
            ->with('success', 'Import terminé avec succès!')
            ->with('console_output', $output);
    }



    public function export(Request $request): BinaryFileResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        Artisan::call('app:export-purchases-report', [
            '--date_from' => $request->date_from,
            '--date_to' => $request->date_to
        ]);

        $output = trim(Artisan::output());
        $filePath = str_replace('Fichier disponible: ', '', $output);

        return response()->download(storage_path("app/{$filePath}"))
            ->deleteFileAfterSend();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchases $purchases)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchases $purchases)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchases $purchases)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchases $purchases)
    {
        //
    }
}
