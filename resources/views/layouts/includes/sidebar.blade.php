<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="/assets/images/logo-dark-sm.png" alt="" height="26">
            </span>
            <span class="logo-lg">
                <img src="/assets/images/logo-dark.png" alt="" height="28">
            </span>
        </a>

        <a href="index.html" class="logo logo-light">
            <span class="logo-lg">
                <img src="/assets/images/logo-light.png" alt="" height="30">
            </span>
            <span class="logo-sm">
                <img src="/assets/images/logo-light-sm.png" alt="" height="26">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
        <i class="bx bx-menu align-middle"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Dashboard</li>
                
                @if(auth()->user()->role == 'SUPERADMIN')
                <li>
                    <a href="/school">
                        <i class="bx bxs-school icon nav-icon"></i>
                        <span class="menu-item" data-key="t-school">Sekolah</span>
                    </a>
                </li>
                
                <li>
                    <a href="/admin">
                        <i class="bx bxs-group icon nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Admin</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role == 'ADMIN')
                <li>
                    <a href="/home">
                        <i class="bx bxs-home icon nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Beranda</span>
                    </a>
                </li>
                <li>
                    <a href="/me/school">
                        <i class="bx bxs-school icon nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Sekolah</span>
                    </a>
                </li>

                <li>
                    <a href="/admin">
                        <i class="bx bxs-group icon nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Admin</span>
                    </a>
                </li>
                
                <li>
                    <a href="/grade">
                        <i class="bx bxs-buildings icon nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="/student">
                        <i class="bx bxs-graduation nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Siswa</span>
                    </a>
                </li>
                @endif

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->