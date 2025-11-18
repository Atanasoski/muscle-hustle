# 🚀 Quick Start Guide - Muscle Hustle

Get up and running in 5 minutes!

## ⚡ Fast Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment (already done - using SQLite)
# The .env file is already configured

# 3. Build frontend assets
npm run build

# 4. Start the server
php artisan serve
```

That's it! Visit **http://localhost:8000** 🎉

## 🔐 Login

The database already has a test user:
- **Email**: test@example.com
- **Password**: password

Or register a new account at `/register`

## 📋 First Steps

### 1️⃣ Check Out the Dashboard
- Login and you'll see the dashboard
- Browse the navigation to explore features

### 2️⃣ View Available Exercises
- Go to **Templates** → **New Template**
- Click **Edit** on your template
- Click **Add Exercise** to see 60+ pre-loaded exercises!

### 3️⃣ Create Your First Workout
1. **Templates** → **New Template**
2. Name it (e.g., "Push Day")
3. Click **Create Template**
4. Click **Add Exercise** and choose exercises
5. Set target sets/reps/weight
6. **Drag exercises** to reorder them ✨

### 4️⃣ Plan Your Week
1. Go to **Planner** → **Workouts**
2. Click **Assign Workout** on any day
3. Select your template
4. Done! Now it shows in the weekly view

### 5️⃣ Start a Workout
1. Go to **Today's Workout** (or click **Start Workout** from dashboard)
2. Click **Start Workout**
3. Log sets with weight and reps
4. Use the **Rest Timer** between sets ⏱️
5. Click **Complete Workout** when done

### 6️⃣ Plan Meals (Optional)
1. Go to **Planner** → **Meals**
2. Click **Add** on any meal slot
3. Enter meal name and macros
4. View your entire week at a glance

## 💡 Pro Tips

- **Drag & Drop**: In template editor, grab the grip icon to reorder exercises
- **Last Weight**: When logging sets, you'll see your last weight for that exercise
- **Rest Timer**: Exercises have default rest times, or start a custom timer
- **Quick Actions**: Dashboard has shortcuts to common actions
- **Auto-Increment**: Set numbers auto-increment as you log

## 🎯 Example Workout Flow

```
1. Monday morning: Check dashboard
2. See "Push Day" is scheduled for today
3. Click "Start Workout"
4. Log Bench Press: 60kg × 10 reps → Log Set → Start Timer (90s)
5. Rest, then log Set 2: 60kg × 9 reps
6. Continue through all exercises
7. Add notes: "Felt strong today!"
8. Complete Workout ✅
```

## 🔧 Development Mode

For development with hot reload:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (optional)
npm run dev
```

## 🗄️ Database Commands

```bash
# Reset database
php artisan migrate:fresh --force

# Reseed exercises
php artisan db:seed --class=ExerciseSeeder --force

# Reset everything
php artisan migrate:fresh --seed --force
```

## 🎨 Customize

### Add Your Own Exercises

Edit `database/seeders/ExerciseSeeder.php`:

```php
['name' => 'My Custom Exercise', 'category' => 'Custom', 'default_rest_sec' => 90],
```

Then run:
```bash
php artisan db:seed --class=ExerciseSeeder --force
```

### Change App Name

Edit `.env`:
```
APP_NAME="My Fitness App"
```

## 📱 Mobile Friendly

The app is fully responsive! Open it on your phone while working out at the gym.

## 🐛 Troubleshooting

**Issue**: "Class 'App\Models\...' not found"
```bash
composer dump-autoload
```

**Issue**: Styles not loading
```bash
npm run build
```

**Issue**: Database errors
```bash
php artisan migrate:fresh --force
php artisan db:seed --class=ExerciseSeeder --force
```

**Issue**: Permission errors on Mac
```bash
chmod -R 775 storage bootstrap/cache
```

## 🎓 Learn More

- Full documentation in `README.md`
- Check `routes/web.php` for all available routes
- Check `app/Http/Controllers/` for controller logic

---

**Now go crush your workouts! 💪**

