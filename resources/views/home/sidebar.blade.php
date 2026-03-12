<style>
    /* Sidebar active item - darker text */
    .main-sidebar .sidebar-menu li.active>a,
    .main-sidebar .sidebar-menu li.active>a span,
    .main-sidebar .sidebar-menu li.active>a i {
        color: #2c2c2c !important;
        /* dark text */
        font-weight: 600;
    }

    /* Active dropdown parent */
    .main-sidebar .sidebar-menu li.dropdown.active>a {
        background-color: #f5f5f5 !important;
    }

    /* Active dropdown child */
    .main-sidebar .sidebar-menu li.dropdown ul.dropdown-menu li.active>a {
        color: #2c2c2c !important;
        background-color: #eaeaea !important;
    }
</style>

<div class="main-sidebar sidebar-style-3" style="background-color: #1F5036;">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">CPSU DOCTRACK</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">CP</a>
        </div>

        <ul class="sidebar-menu">

            {{-- ================= Add Transaction (Admin / Records Officer only) ================= --}}
            @if (in_array(auth()->user()->role, ['Administrator', 'records_officer']))
                <li class="menu-header">Transaction</li>
                <div class="pl-2 pr-2 hide-sidebar-mini">
                    <button class="btn btn-primary btn-lg btn-block btn-icon-split" data-toggle="modal"
                        data-target="#exampleModal"><i class="fas fa-plus"></i> Add Transaction</button>
                </div>
            @endif


            {{-- ================= Sidebar for Super User ================= --}}
            @if (auth()->user()->role === 'super_user')
                {{-- Document for Action first --}}
                <li class="dropdown {{ request()->routeIs('routing*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-circle-exclamation"></i>
                        <span>Document for Action</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('routingToPres') ? 'active show' : '' }}">
                            <a class="nav-link" href="{{ route('routingToPres') }}">Routed To President</a>
                        </li>
                    </ul>
                </li>

                {{-- Home --}}
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="far fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>

                {{-- Pending --}}
                <li class="{{ request()->routeIs('pending') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('pending') }}">
                        <i class="far fa-hourglass"></i>
                        <span>Pending</span>
                    </a>
                </li>

                {{-- Print Logbook --}}
                <li><a class="nav-link" href="blank.html"><i class="fas fa-print"></i> <span>Print Logbook</span></a>
                </li>

                {{-- Audit Trail & Logs --}}
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-clock-rotate-left"></i>
                        <span>Audit Trail & Logs</span></a>
                    <ul class="dropdown-menu">
                        <li><a href="gmaps-advanced-route.html">Transaction Logs</a></li>
                        <li><a href="gmaps-draggable-marker.html">Management Logs</a></li>
                    </ul>
                </li>
            @endif

            {{-- ================= Sidebar for Staff ================= --}}
            @if (auth()->user()->role === 'staff')
                {{-- Home --}}
                <li class="menu-header">Transaction</li>
                <div class="pl-2 pr-2 hide-sidebar-mini">
                    <button class="btn btn-primary btn-lg btn-block btn-icon-split" data-toggle="modal"
                        data-target="#interOfficeModal"><i class="fas fa-plus"></i> Add Transaction</button>
                </div>
                <li class="menu-header">Menu</li>
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="far fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>

                {{-- Pending --}}
                <li class="{{ request()->routeIs('pending') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('pending') }}">
                        <i class="far fa-hourglass"></i>
                        <span>Pending</span> <small
                            class="badge badge-warning text-primary text-bold">{{ $pendingCount ?? 0 }}</small>
                    </a>
                </li>

                <li class="{{ request()->routeIs('interOffice') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('interOffice') }}">
                        <i class="fas fa-user-check"></i>
                        <span>Inter-Office Files</span>
                        <small class="badge badge-warning text-primary text-bold">{{ $interOfficeCount ?? 0 }}</small>
                    </a>
                </li>

                {{-- Print Logbook --}}
                <li><a class="nav-link" href="blank.html"><i class="fas fa-print"></i> <span>Print Logbook</span></a>
                </li>

                {{-- Audit Trail & Logs --}}
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-clock-rotate-left"></i>
                        <span>Audit Trail & Logs</span></a>
                    <ul class="dropdown-menu">
                        <li><a href="gmaps-advanced-route.html">Transaction Logs</a></li>
                        <li><a href="gmaps-draggable-marker.html">Management Logs</a></li>
                    </ul>
                </li>
            @endif

            {{-- ================= Sidebar for Admin / Records Officer ================= --}}
            @if (in_array(auth()->user()->role, ['Administrator', 'records_officer']))
                {{-- Menu Header --}}
                <li class="menu-header">Menu</li>

                {{-- Home --}}
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="far fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>

                {{-- Document for Action --}}
                <li class="dropdown {{ request()->routeIs('routing*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-circle-exclamation"></i>
                        <span>Document for Action</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('routingToPres') ? 'active show' : '' }}">
                            <a class="nav-link" href="{{ route('routingToPres') }}">Routed To President</a>
                        </li>
                        <li class="{{ request()->routeIs('routing') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('routing') }}">Routed Back To Records</a>
                        </li>
                        <li class="{{ request()->routeIs('routingPending') ? 'active show' : '' }}">
                            <a class="nav-link" href="{{ route('routingPending') }}">Pending Route</a>
                        </li>
                    </ul>
                </li>

                {{-- Pending --}}
                <li class="{{ request()->routeIs('pending') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('pending') }}">
                        <i class="far fa-hourglass"></i>
                        <span>Pending <small
                                class="badge badge-warning text-primary text-bold">{{ $pendingCount ?? 0 }}</small>

                    </a>
                </li>

                {{-- Print Logbook --}}
                <li><a class="nav-link" href="blank.html"><i class="fas fa-print"></i> <span>Print Logbook</span></a>
                </li>


                {{-- Distribution List --}}
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-columns"></i>
                        <span>Distribution List</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a href="{{ route('viewistListPres') }}"
                                class="nav-link {{ request()->routeIs('viewistListPres') ? 'active' : '' }}">
                                Routed From President
                            </a>
                        </li>

                        <li><a href="{{ route('viewDirectList') }}"
                                class="nav-link {{ request()->routeIs('viewistListPres') ? 'active' : '' }}">Direct to
                                Personnels</a></li>
                    </ul>
                </li>

                <li class="menu-header">Office - To - Office</li>
                <li class="{{ request()->routeIs('interOffice') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('interOffice') }}">
                        <i class="fas fa-user-check"></i>
                        <span>Inter-Office Files</span>
                        <small class="badge badge-warning text-primary text-bold">{{ $interOfficeCount ?? 0 }}</small>
                    </a>
                </li>

                {{-- Management --}}
                <li class="menu-header">Management</li>
                <li class="{{ request()->routeIs('usersView') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('usersView') }}"><i class="fas fa-users-gear"></i> <span>User
                            Management</span></a></li>
                <li
                    class="dropdown {{ request()->routeIs('offices*') || request()->routeIs('userGroups*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i>
                        <span>Roles and Groupings</span></a>
                    <ul
                        class="dropdown-menu {{ request()->routeIs('offices*') || request()->routeIs('userGroups*') ? 'show' : '' }}">
                        <li>
                            <a class="nav-link {{ request()->routeIs('offices*') ? 'active' : '' }}"
                                href="{{ route('offices') }}">
                                Office List
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('userGroups*') ? 'active' : '' }}"
                                href="{{ route('userGroups') }}">
                                Group List
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown {{ request()->routeIs('archiveLogbook', 'archivedHistory') ? 'active' : '' }}">
                    <a class="nav-link has-dropdown" href="#"><i class="fas fa-tools"></i> <span>Archived
                            Settings</span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link {{ request()->routeIs('archiveLogbook') ? 'active' : '' }}"
                                href="{{ route('archiveLogbook') }}" target="_blank">Archive Logbook</a></li>
                        <li><a class="nav-link {{ request()->routeIs('archivedHistory') ? 'active' : '' }}"
                                href="{{ route('archivedHistory') }}" target="_blank">Archived History</a></li>
                        <li><a class="nav-link {{ request()->routeIs('archivedHistory') ? 'active' : '' }}"
                                href="{{ route('archivedHistory') }}" target="_blank">Archived File Folders</a></li>

                    </ul>
                </li>

                {{-- Audit Logs (bottom for Admin / Records Officer) --}}
                <li
                    class="dropdown {{ request()->routeIs('tranLogsView', 'managementLogs', 'logsView') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>Audit Trail & Logs</span>
                    </a>
                    <ul
                        class="dropdown-menu {{ request()->routeIs('tranLogsView', 'managementLogs', 'logsView') ? 'show' : '' }}">
                        <li>
                            <a class="nav-link {{ request()->routeIs('tranLogsView') ? 'active' : '' }}"
                                href="{{ route('tranLogsView') }}" target="_blank">
                                Transaction Logs
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('managementLogs') ? 'active' : '' }}"
                                href="{{ route('managementLogs') }}" target="_blank">
                                Management Logs
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('logsView') ? 'active' : '' }}"
                                href="{{ route('logsView') }}" target="_blank">
                                System Logs
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

        </ul>




    </aside>
</div>

@include('modal.addTransaction')
@include('modal.addInterOffice')
