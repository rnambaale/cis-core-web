<div class="side-nav">
    <div class="side-nav-inner">
        <div class="side-nav-logo">
            <a href="{{ route('home') }}">
                <div class="logo logo-dark" style="background-image: url('{{ asset('espire/images/logo/logo.png') }}')"></div>
                <div class="logo logo-white" style="background-image: url('{{ asset('espire/images/logo/logo-white.png') }}')"></div>
            </a>
            <div class="mobile-toggle side-nav-toggle">
                <a href="">
                    <i class="ti-arrow-circle-left"></i>
                </a>
            </div>
        </div>
        <ul class="side-nav-menu scrollable">
            <li class="nav-item">
                <a class="mrg-top-30" href="#">
                    <span class="icon-holder">
                        <i class="ti-home"></i>
                    </span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                @if(auth_any('uncategorized'))
                    <a class="dropdown-toggle" href="javascript:void(0);">
                        <span class="icon-holder">
                                <i class="ti-package"></i>
                            </span>
                        <span class="title">Uncategorized</span>
                        <span class="arrow">
                            <i class="ti-angle-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        @if(auth_has('facilities'))
                            <li>
                                <a href="{{ route('facilities.index') }}">Facilities</a>
                            </li>
                        @endif
                        @if(auth_has('modules'))
                            <li>
                                <a href="#">Modules</a>
                            </li>
                        @endif
                        @if(auth_has('permissions'))
                            <li>
                                <a href="#">Permissions</a>
                            </li>
                        @endif
                        @if(auth_has('roles'))
                            <li>
                                <a href="#">Roles</a>
                            </li>
                        @endif
                        @if(auth_has('users'))
                            <li>
                                <a href="#">Users</a>
                            </li>
                        @endif
                    </ul>
                @endif
            </li>
        </ul>
    </div>
</div>
