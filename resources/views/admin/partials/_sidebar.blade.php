<div class="sidebar-nav-header">
    <h4 class="title">Spumoni Admin</h4>
    <button  class="btn menu-toggler-button"
        data-toggle="collapse" 
        type="button" data-target="#collapse-side-nav">
        <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
        </span>
    </button>
</div>

<div id="collapse-side-nav" class="collapse-side-nav collapse">
    <div class="globalAdminNav">
        <h5>Global Admin</h5>
        <ul>
            <li>
                <a href="{{ route('admin') }}">
                    <span class="glyphicon icon-dashboard" aria-hidden="true"></span>
                    Dashboard

                </a>
            </li>
            <li>
                <a href="{{ route('admin.stores') }}">
                    <span class="glyphicon icon-stores" aria-hidden="true"></span>
                    Stores</a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}">
                    <span class="glyphicon icon-users" aria-hidden="true"></span>
                    Users
                </a>
            </li>
        </ul>
    </div>
    
    <div class="storeManagementNav">
        <h5>Store Management</h5>
        <label class="store_picker_label">Change Store</label>

        <select class="form-control" id="store_context_picker" name="store_context_picker" onchange="window.location.href = '/admin/change-store-context/' + this.value;">
            <option value="">Select a store</option>
            @foreach(auth()->user()->storeFormList() as $id => $store)
                <option value="{{ $id }}" @if(auth()->user()->currentStore && $id == auth()->user()->currentStore->id) selected="selected" @endif>{{ $store }}</option>
            @endforeach
        </select>

        @if(auth()->user()->currentStore)
            <ul id="storeNav">
                <li>
                    <a href="{{ route('admin.store.dashboard', auth()->user()->currentStore) }}" dusk="store-dashboard-link">
                        Dashboard
                    </a>
                </li>
                <li>

                    <a href="{{ route('admin.products.index', auth()->user()->currentStore) }}" dusk="store-products-link">
                        Products
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.categories.index', auth()->user()->currentStore) }}" dusk="store-product-categories-link">
                        Product Categories
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.store.pages', auth()->user()->currentStore) }}" dusk="store-pages-link">
                        Pages
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.photo-booth.index', auth()->user()->currentStore) }}">
                        Photo Booth
                    </a>
                </li>
                <li class="button-nav">
                    <a href="{{ route('store', auth()->user()->currentStore) }}" dusk="store-assets-link" class="btn outlined" target="_blank">View Storefront</a>
                </li>
            </ul>
        @endif
    </div>
</div>