@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-6">Import des achats</h1>

    <form action="{{ route('purchases.import') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label for="supplier_id" class="block text-gray-700 mb-2">Fournisseur</label>
            <select name="supplier_id" id="supplier_id" class="w-full p-2 border rounded" required>
                <option value="">Sélectionnez un fournisseur</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="csv_file" class="block text-gray-700 mb-2">Fichier CSV</label>
            <input type="file" name="csv_file" id="csv_file" class="w-full p-2 border rounded" accept=".csv,.txt" required>
            <p class="text-sm text-gray-500 mt-1">
                Format attendu: reference_produit;nom_produit;quantite;prix_unitaire_ht;numero_facture
            </p>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Importer</button>
            <a href="{{ asset('storage/donnee_test_gestion_achat.csv') }}" class="text-blue-500 hover:underline">Télécharger un exemple</a>
        </div>
    </form>

    @if(session('success'))
    <div class="mt-4 p-4 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('console_output'))
    <div class="mt-4 p-4 bg-gray-100 rounded">
        <pre class="whitespace-pre-wrap">{{ session('console_output') }}</pre>
    </div>
    @endif
</div>
@endsection