<ul class="sections-nav text-center d-flex">
    <li class="{{ (Route::is('pharmacy.inventories.index', $storeId))  ? 'active' : '' }}">
        <a href="{{ route('pharmacy.inventories.index', $storeId) }}" title="Inventory">Inventory</a>
    </li>
    <li class="{{ (Route::is('pharmacy.sales.create', $storeId))  ? 'active' : '' }}">
        <a href="{{ route('pharmacy.sales.create', $storeId) }}" title="Sales">Sales</a>
    </li>
    <li class="{{ (Route::is('pharmacy.purchases.create', $storeId))  ? 'active' : '' }}">
        <a href="{{ route('pharmacy.purchases.create', $storeId) }}" title="Purchases">Purchases</a>
    </li>
</ul>