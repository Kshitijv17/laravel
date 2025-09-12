<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top navbar-scroll">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fab fa-linkedin fa-2x"></i></a>

    <form class="input-group" style="width: 400px">
      <input type="search" class="form-control" placeholder="Type query" aria-label="Search" />
      <button class="btn btn-outline-primary" type="button" style="padding: .45rem 1.5rem .35rem;">
        Search
      </button>
    </form>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
      aria-label="Toggle navigation">
      <i class="fas fa-bars"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link text-center active" href="{{ route('user.dashboard') }}"><i class="fas fa-home fa-lg my-2"></i><span class="small">Home</span></a></li>
        <li class="nav-item"><a class="nav-link text-center" href="#"><i class="fas fa-user-friends fa-lg my-2"></i><span class="small">Network</span></a></li>
        <li class="nav-item"><a class="nav-link text-center" href="#"><i class="fas fa-briefcase fa-lg my-2"></i><span class="small">Jobs</span></a></li>
        <li class="nav-item"><a class="nav-link text-center" href="#"><i class="fas fa-comment-dots fa-lg my-2"></i><span class="small">Messages</span></a></li>
        <li class="nav-item"><a class="nav-link text-center" href="#"><i class="fas fa-bell fa-lg my-2"></i><span class="small">Alerts</span></a></li>

        @auth('web')
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}" class="rounded-circle" height="30" alt="Avatar" />
            <span class="ms-2">{{ Auth::user()->name }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('user.profile') }}">My Profile</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li>
              <form action="{{ route('user.logout') }}" method="POST">
                @csrf
                <button class="dropdown-item text-danger">Logout</button>
              </form>
            </li>
          </ul>
        </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>
