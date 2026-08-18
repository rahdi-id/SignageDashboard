<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">Digital Signage</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">DS</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Route::is('dashboard') ? 'active' : '' }}"><a class="nav-link" href="{{ route('dashboard') }}"><i
                        class="fas fa-home"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="{{ Route::is('location.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('location.index') }}"><i
                        class="fas fa-map-pin"></i>
                    <span>Location</span></a>
            </li>
            <li class="{{ Route::is('display.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('display.index') }}"><i
                        class="fas fa-tv"></i>
                    <span>Display</span></a>
            </li>
            <li class="{{ Route::is('event.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('event.index') }}"><i
                        class="fas fa-list"></i>
                    <span>Event</span></a>
            </li>
            <li class="{{ Route::is('promotion.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('promotion.index') }}"><i
                        class="fas fa-percent"></i>
                    <span>Promotion</span></a>
            </li>
            <li class="{{ Route::is('admin.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.index') }}"><i
                        class="fas fa-users-cog"></i>
                    <span>Administrator</span></a>
            </li>

            {{-- Hotel Helpdesk --}}
            <li class="menu-header">Hotel Helpdesk</li>
            <li class="{{ Route::is('helpdesk.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('helpdesk.dashboard') }}">
                    <i class="fas fa-headset"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ Route::is('helpdesk.conversations.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('helpdesk.conversations.index') }}">
                    <i class="fas fa-comments"></i>
                    <span>Conversations</span>
                </a>
            </li>
            <li class="{{ Route::is('helpdesk.departments.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('helpdesk.departments.index') }}">
                    <i class="fas fa-building"></i>
                    <span>Departments</span>
                </a>
            </li>
        </ul>
    </aside>
</div>
