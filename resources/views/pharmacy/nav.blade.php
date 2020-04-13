<ul class="sections-nav text-center d-flex">
    <li class="{{ ($section == 'inventory')  ? 'active' : '' }}">
        <a href="{{ route('pharmacy.inventories.index', $storeId) }}" title="Inventory">Inventory</a>
    </li>
    <li class="{{ ($section == 'sales')  ? 'active' : '' }}">
        <a href="{{ route('pharmacy.sales.create', $storeId) }}" title="Sales">Sales</a>
    </li>
    <li class="{{ ($section == 'purchases')  ? 'active' : '' }}">
        <a href="{{ route('pharmacy.purchases.create', $storeId) }}" title="Purchases">Purchases</a>
    </li>
</ul>