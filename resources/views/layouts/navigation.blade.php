<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="bi bi-lightning-charge-fill"></i>
            <img src="{{ asset('images/muscle-hustle-logo.png') }}" alt="Muscle Hustle" style="height: 50px; width: auto;">
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
                    
                    <!-- Workouts Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs(['workout-templates.*', 'exercises.*', 'planner.workouts', 'workouts.*']) ? 'active' : '' }}" 
                           href="#" id="workoutsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-lightning-charge-fill"></i> Workouts
                        </a>
                        <ul class="dropdown-menu dropdown-menu-icons">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('planner.workouts') }}">
                                    <i class="bi bi-calendar-week fs-5 text-danger"></i>
                                    <span>Weekly Planner</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('workout-templates.index') }}">
                                    <i class="bi bi-journal-text fs-5 text-primary"></i>
                                    <span>Templates</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('exercises.index') }}">
                                    <i class="bi bi-list-ul fs-5 text-info"></i>
                                    <span>Exercises</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Nutrition Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs(['recipes.*', 'foods.*', 'planner.meals', 'planner.food-diary', 'planner.grocery-list']) ? 'active' : '' }}" 
                           href="#" id="nutritionDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-egg-fried"></i> Nutrition
                        </a>
                        <ul class="dropdown-menu dropdown-menu-icons">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('planner.meals') }}">
                                    <i class="bi bi-calendar-week fs-5 text-success"></i>
                                    <span>Meal Planner</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('planner.food-diary') }}">
                                    <i class="bi bi-journal-text fs-5 text-primary"></i>
                                    <span>Food Diary</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('recipes.index') }}">
                                    <i class="bi bi-book fs-5 text-warning"></i>
                                    <span>Recipes</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('foods.index') }}">
                                    <i class="bi bi-database fs-5 text-info"></i>
                                    <span>Foods</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('planner.grocery-list') }}">
                                    <i class="bi bi-cart3 fs-5 text-success"></i>
                                    <span>Grocery List</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Partners (Admin) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('partners.*') ? 'active' : '' }}" href="{{ route('partners.index') }}">
                            <i class="bi bi-building"></i> Partners
                        </a>
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
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" 
                                     class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle me-1"></i>
                            @endif
                            {{ Auth::user()->name }}
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

