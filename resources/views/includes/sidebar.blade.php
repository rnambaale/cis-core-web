<aside class="sidebar">
    <nav class="sidebar-nav">
        <ul class="metismenu" id="sidemenu" style="display:none;">
            
            <li class="menu-title">Navigation</li>

            <li class="{{ Route::is('home') ? 'active mm-active' : '' }}">
                <a href="#" aria-expanded="{{ Route::is('home') ? 'true' : 'false' }}">
                    <span class="fa fa-tachometer-alt"></span>
                    <span class="">Dashboards</span>
                    <span class="fa arrow"></span>
                </a>
                <ul aria-expanded="true">
                    <li>
                        <a href="{{ route('home') }}">Home </a>
                    </li>
                </ul>
            </li>

            
            <li class="menu-title">Pharmacy</li>

            <li class="{{ (Route::is('pharmacy.stores.index') || Route::is('sales.index'))  ? 'mm-active' : '' }}">
                <a href="#" aria-expanded="true">
                    <span class="fa fa-database"></span>
                    <span class="">Pharmacy</span>
                    <span class="fa arrow"></span>
                </a>
                <ul aria-expanded="true">
                    <li class="{{ Route::is('pharmacy.stores.index') ? 'active' : '' }}">
                        <a href="{{ route('pharmacy.stores.index') }}"> Stores</a>
                    </li>
                    <li>
                        <a href="#"> Reports</a>
                    </li>
                </ul>
            </li>

            @if(auth_any('uncategorized'))
            <li class="menu-title">Admin</li>

            <li
                class="{{ (Route::is('facilities.index') || Route::is('modules.index') || Route::is('permissions.index') || Route::is('roles.index') || Route::is('users.index'))  ? 'mm-active' : '' }}"
                >
                
                <a href="#" aria-expanded="false">Un Categorised <span class="fa arrow"></span></a>

                <ul aria-expanded="false">
                    @if(auth_has('facilities'))
                        <li class="{{ Route::is('facilities.index') ? 'active' : '' }}">
                            <a href="{{ route('facilities.index') }}">Facilities</a>
                        </li>
                    @endif
                    @if(auth_has('modules'))
                        <li class="{{ Route::is('modules.index') ? 'active' : '' }}">
                            <a href="{{ route('modules.index') }}">Modules</a>
                        </li>
                    @endif
                    @if(auth_has('permissions'))
                        <li class="{{ Route::is('permissions.index') ? 'active' : '' }}">
                            <a href="{{ route('permissions.index') }}">Permissions</a>
                        </li>
                    @endif
                    @if(auth_has('roles'))
                        <li class="{{ Route::is('roles.index') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}">Roles</a>
                        </li>
                    @endif
                    @if(auth_has('users'))
                        <li class="{{ Route::is('users.index') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}">Users</a>
                        </li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>
    </nav>
</aside>
  
  