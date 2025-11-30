<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-lightning-charge-fill"></i> Muscle Hustle
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door-fill"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('workout-templates.*') ? 'active' : '' }}" href="{{ route('workout-templates.index') }}">
                            <i class="bi bi-journal-text"></i> Templates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('exercises.*') ? 'active' : '' }}" href="{{ route('exercises.index') }}">
                            <i class="bi bi-list-ul"></i> Exercises
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('recipes.*') ? 'active' : '' }}" href="{{ route('recipes.index') }}">
                            <i class="bi bi-book"></i> Recipes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}" href="{{ route('foods.index') }}">
                            <i class="bi bi-database"></i> Foods
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('planner.*') ? 'active' : '' }}" href="#" id="plannerDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar-week-fill"></i> Planner
                        </a>
                        <ul class="dropdown-menu dropdown-menu-icons">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('planner.workouts') }}">
                                    <i class="bi bi-lightning-charge-fill fs-5 text-danger"></i>
                                    <span>Workouts</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('planner.meals') }}">
                                    <i class="bi bi-egg-fried fs-5 text-success"></i>
                                    <span>Meals</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
            <ul class="navbar-nav align-items-center">
                @auth
                    <li class="nav-item me-3">
                        <div class="theme-toggle" onclick="toggleTheme()">
                            <i id="theme-icon" class="bi bi-sun-fill"></i>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear"></i> Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
