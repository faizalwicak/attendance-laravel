<header id="page-topbar" class="isvertical-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/assets/images/logo-dark-sm.png" alt="" height="26">
                    </span>
                    <span class="logo-lg">
                        <img src="/assets/images/logo-dark-sm.png" alt="" height="26">
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

            <!-- start page title -->
            <div class="page-title-box align-self-center d-none d-md-block">
                <h4 class="page-title mb-0">{{ $title }}</h4>
            </div>
            <!-- end page title -->

        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown-v" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    @if (auth()->user()->image != null && auth()->user()->image != '')
                        <img class="rounded-circle header-profile-user" src="/images/{{ auth()->user()->image }}"
                            alt="Header Avatar">
                    @else
                        <img class="rounded-circle header-profile-user" src="/assets/images/default-user.png"
                            alt="Header Avatar">
                    @endif
                    <span class="d-none d-xl-inline-block ms-2 fw-medium font-size-15">{{ auth()->user()->name }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="p-3 border-bottom">
                        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                        <p class="mb-0 font-size-11 text-muted">{{ auth()->user()->email }}</p>
                    </div>
                    <a class="dropdown-item" href="/me/profile"><i
                            class="mdi mdi-account-circle text-muted font-size-16 align-middle me-2"></i> <span
                            class="align-middle">Update Profile</span></a>
                    <a class="dropdown-item" href="/me/password""><i
                            class="mdi mdi-key-outline text-muted font-size-16 align-middle me-2"></i> <span
                            class="align-middle">Ganti Password</span></a>
                    <div class="dropdown-divider"></div>
                    @if (in_array(auth()->user()->role, ['ADMIN', 'SUPERADMIN']))
                        <a class="dropdown-item" href="/backup""><i
                                class="mdi mdi-database text-muted font-size-16 align-middle me-2"></i> <span
                                class="align-middle">Backup DB</span></a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" href="/logout"><i
                            class="mdi mdi-logout text-muted font-size-16 align-middle me-2"></i> <span
                            class="align-middle">Logout</span></a>
                </div>
            </div>
        </div>
    </div>
</header>
