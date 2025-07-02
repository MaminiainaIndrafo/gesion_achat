@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Tableau de Bord Gestion d'Achats</h1>

    <!-- Section des Actions Rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Carte Import CSV -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Importation CSV</h2>
            <form action="{{ route('purchases.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                    <select name="supplier_id" id="supplier_id" class="w-full p-2 border rounded-md" required>
                        <option value="">Sélectionnez un fournisseur</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">Fichier CSV</label>
                    <input type="file" name="csv_file" id="csv_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition duration-200">Importer</button>
            </form>
        </div>

        <!-- Carte Synchronisation API -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Synchronisation API</h2>
            <p class="text-sm text-gray-600 mb-4">Mise à jour des prix et stocks via API</p>
            <form action="{{ route('api.sync') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md transition duration-200">Lancer la Synchro</button>
            </form>
            <div class="mt-4">
                <p class="text-sm text-gray-600">Dernière synchro : 
                    <span class="font-medium">{{ $lastSync ?? 'Jamais' }}</span>
                </p>
            </div>
        </div>

        <!-- Carte Statistiques -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Aperçu Rapide</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Produits</span>
                    <span class="font-medium">{{ $productsCount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Fournisseurs</span>
                    <span class="font-medium">{{ $suppliersCount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Stock Moyen</span>
                    <span class="font-medium">{{ $averageStock }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Marge Moyenne</span>
                    <span class="font-medium">{{ $averageMargin }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Rapport des Marges -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Rapport des Marges</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Achat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Vente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marge</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->purchase_price, 2) }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->selling_price, 2) }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $product->margin >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($product->margin, 2) }} € ({{ number_format($product->margin_percent, 2) }}%)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="{{ $product->stock->quantity <= $product->stock->alert_threshold ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                {{ $product->stock->quantity }}
                            </span>
                            @if($product->stock->quantity <= $product->stock->alert_threshold)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Alerte</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Section Fournisseurs -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Liste des Fournisseurs</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->contact_email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->contact_phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->products_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('suppliers.show', $supplier->supplier_id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                            <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="text-indigo-600 hover:text-indigo-900">Éditer</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('error') }}
    </div>
    @endif
</div>

<!-- AlpineJS pour les interactions -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection