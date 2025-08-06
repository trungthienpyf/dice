<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-3">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dice*') ? 'active' : '' }}" href="{{ route('dice.index') }}">🎲 Dice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">👤 Users</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <span class="me-3 text-muted">👋 Xin chào, <strong>{{ auth()->user()->username ?? 'Người dùng' }}</strong></span>
                <form action="{{ route('users.logout') }}" method="GET">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</nav>
