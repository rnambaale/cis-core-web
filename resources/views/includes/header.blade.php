<div class="header navbar">
    <div class="header-container">
        <ul class="nav-left">
            <li>
                <a class="side-nav-toggle" href="javascript:void(0);">
                    <i class="ti-view-grid"></i>
                </a>
            </li>
        </ul>
        <ul class="nav-right">
            <li class="user-profile dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img class="profile-img img-fluid" src="{{ asset('espire/images/user.jpg') }}" alt="">
                    <div class="user-info">
                        <span class="name pdd-right-5">{{ Auth::user()->name }}</span>
                        <i class="ti-angle-down font-size-10"></i>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#">
                            <i class="ti-user pdd-right-10"></i>
                            <span>{{ __('Profile') }}</span>
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti-power-off pdd-right-10"></i>
                            <span>{{ __('Logout') }}</span>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
