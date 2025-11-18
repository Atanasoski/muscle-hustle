# 🏋️ Muscle Hustle - Fitness Tracking Web App

A comprehensive fitness tracking web application built with **Laravel 11**, **Blade templates**, and **Bootstrap 5**. Track your workouts, plan your week, log your meals, and monitor your progress!

## ✨ Features

### 🏋️‍♂️ Workout Management
- **Workout Templates**: Create reusable workout templates with exercises
- **Drag & Drop Ordering**: Reorder exercises in templates using SortableJS
- **Weekly Planning**: Assign workouts to specific days of the week
- **Live Workout Sessions**: Start and log real-time workout sessions
- **Rest Timer**: Built-in countdown timer between sets
- **Progress Tracking**: See your last logged weights for each exercise

### 🍳 Meal Planning
- **Weekly Meal Planner**: Plan all meals for the week (breakfast, lunch, dinner, snacks)
- **Macro Tracking**: Track calories, protein, carbs, and fats
- **Easy Management**: Quick add/edit/delete meals for any day

### 📊 Dashboard
- View today's workout at a glance
- Weekly overview of your workout plan
- Quick access to all features

## 🚀 Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates + Bootstrap 5
- **Database**: MySQL 8+ / SQLite (default)
- **Authentication**: Laravel Breeze (Blade version)
- **JavaScript**: Vanilla JS + SortableJS for drag-and-drop

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8+ (optional - SQLite is configured by default)
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
```bash
git clone <your-repo-url>
cd muscle-hustle
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database** (Optional - SQLite is already configured)

For MySQL, update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=muscle_hustle
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations**
```bash
php artisan migrate --force
```

7. **Seed the database with exercises**
```bash
php artisan db:seed --class=ExerciseSeeder --force
```

8. **Build assets**
```bash
npm run build
```

9. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser!

## 🎯 Usage Guide

### Getting Started

1. **Register an Account**: Create your user account via the registration page
2. **Create Your First Template**: Go to "Templates" → "New Template"
3. **Add Exercises**: Edit your template and add exercises from the global exercise library
4. **Plan Your Week**: Visit "Planner" → "Workouts" to assign templates to specific days
5. **Start Working Out**: Go to "Today's Workout" and click "Start Workout"

### Creating a Workout Template

1. Navigate to **Templates** → **New Template**
2. Enter a name and optional description
3. Optionally assign it to a day of the week
4. Click **Create Template**
5. Add exercises from the global library
6. Set target sets, reps, and weights for each exercise
7. **Drag and drop** to reorder exercises

### Logging a Workout Session

1. Navigate to **Today's Workout**
2. Click **Start Workout**
3. For each exercise:
   - Enter the weight and reps for each set
   - Click **Log Set**
   - Use the **Rest Timer** between sets
4. Add optional notes
5. Click **Complete Workout** when done

### Planning Meals

1. Navigate to **Planner** → **Meals**
2. Click **Add** on any meal slot
3. Enter meal details and macros
4. Save the meal
5. Edit or delete meals as needed

## 🗄️ Database Schema

The application uses the following main tables:

- `users` - User accounts
- `exercises` - Global and user-created exercises
- `workout_templates` - Workout template definitions
- `workout_template_exercises` - Exercises within templates (with ordering)
- `workout_sessions` - Logged workout sessions
- `set_logs` - Individual set logs (weight, reps)
- `meal_plans` - Weekly meal plans
- `meals` - Individual meals with macros

## 🎨 Customization

### Adding Custom Exercises

Users can see all global exercises in the system. To add more global exercises:

1. Edit `database/seeders/ExerciseSeeder.php`
2. Add new exercises to the array
3. Run `php artisan db:seed --class=ExerciseSeeder --force`

### Styling

The app uses Bootstrap 5. To customize styles:
- Edit Bootstrap variables in `resources/css/app.css`
- Add custom CSS in the same file
- Run `npm run build` to compile

## 🔒 Security

- All routes are protected by authentication middleware
- User data is scoped to the authenticated user
- Authorization checks ensure users can only access their own data
- CSRF protection on all forms
- Password hashing with bcrypt

## 🧪 Testing

Run PHPUnit tests (when created):
```bash
php artisan test
```

## 📝 Code Principles

The codebase follows:
- **PSR-12** coding standards
- **RESTful** routing conventions
- **Route model binding** for clean URLs
- **Form Request validation** for input validation
- **Eloquent relationships** for database queries
- **Authorization policies** for access control

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Bootstrap](https://getbootstrap.com) - CSS Framework
- [SortableJS](https://sortablejs.github.io/Sortable/) - Drag & Drop Library
- [Bootstrap Icons](https://icons.getbootstrap.com) - Icon Library

## 📞 Support

For support, please open an issue in the GitHub repository.

---

**Built with ❤️ using Laravel, Blade, and Bootstrap**

Start your fitness journey today! 💪
