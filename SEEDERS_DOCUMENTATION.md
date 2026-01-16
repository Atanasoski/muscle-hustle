# 🌱 Database Seeders Documentation

## Overview

The Fit Nation application includes comprehensive seeders that populate the database with realistic demo data for a complete fitness tracking experience.

## Seeder Classes

### 1. UserSeeder (`database/seeders/UserSeeder.php`)

Creates a demo user account for testing and demonstration.

**Creates:**
- 1 demo user

**Credentials:**
- Email: `demo@example.com`
- Password: `password`
- Email verified: Yes

**Usage:**
```bash
php artisan db:seed --class=UserSeeder
```

---

### 2. ExerciseSeeder (`database/seeders/ExerciseSeeder.php`)

Seeds 56 global exercises across all major muscle groups.

**Exercise Categories:**
- **Chest** (9 exercises): Barbell Bench Press, Dumbbell Bench Press, etc.
- **Back** (9 exercises): Deadlift, Barbell Row, Pull-ups, etc.
- **Legs** (9 exercises): Barbell Squat, Leg Press, Romanian Deadlift, etc.
- **Shoulders** (8 exercises): Overhead Press, Lateral Raises, etc.
- **Arms** (10 exercises): Curls, Extensions, etc.
- **Core** (6 exercises): Plank, Hanging Leg Raises, etc.
- **Cardio** (5 exercises): Running, Cycling, etc.

**Features:**
- All exercises are global
- Realistic rest times (60-180 seconds based on exercise type)
- Proper categorization for easy filtering
- Uses `firstOrCreate` to avoid duplicates

---

### 3. WorkoutTemplateSeeder (`database/seeders/WorkoutTemplateSeeder.php`)

Creates 4 workout templates following an Upper/Lower split routine optimized for muscle building and fat loss.

**Templates Created:**

#### Upper A - Push Focus (Monday)
- Barbell Bench Press: 4×8 @ 60kg
- Barbell Row: 4×8 @ 50kg  
- Dumbbell Shoulder Press: 3×10 @ 20kg
- Pull-ups: 3×10
- Dumbbell Curl: 3×12 @ 12kg
- Overhead Tricep Extension: 3×12 @ 15kg

#### Lower A - Squat Focus (Tuesday)
- Barbell Squat: 4×8 @ 80kg
- Romanian Deadlift: 3×10 @ 60kg
- Leg Press: 3×12 @ 100kg
- Leg Curl: 3×12 @ 40kg
- Plank: 3×45 seconds

#### Upper B - Pull Focus (Thursday)
- Overhead Press: 4×8 @ 40kg
- Lat Pulldown: 4×8 @ 50kg
- Dumbbell Bench Press: 3×10 @ 25kg
- Seated Cable Row: 3×10 @ 45kg
- Lateral Raises: 3×15 @ 10kg
- Dumbbell Curl: 3×12 @ 12kg

#### Lower B - Deadlift Focus (Friday)
- Deadlift: 3×6 @ 100kg
- Front Squat: 3×10 @ 60kg
- Leg Curl: 3×12 @ 40kg
- Leg Extension: 3×12 @ 50kg
- Hanging Leg Raises: 3×15

**Weekly Schedule:**
- Monday: Upper A
- Tuesday: Lower A
- Wednesday: Rest
- Thursday: Upper B
- Friday: Lower B
- Saturday: Rest
- Sunday: Rest

**Features:**
- Proper exercise ordering for optimal performance
- Balanced volume for hypertrophy and strength
- Realistic target weights for beginners to intermediates
- Appropriate rest times for each exercise type

---

### 4. MealPlanSeeder (`database/seeders/MealPlanSeeder.php`)

Creates a complete 7-day meal plan with 4 meals per day (28 total meals).

**Daily Meal Structure:**
- Breakfast
- Lunch
- Dinner
- Snack

**Nutritional Focus:**
- High protein (150-180g per day)
- Moderate carbs (250-350g per day)
- Moderate fats (60-80g per day)
- Total calories: ~2,000-2,200 per day

**Sample Day (Monday):**
- **Breakfast**: Oatmeal with Whey & Banana (450 cal, 35g protein)
- **Lunch**: Grilled Chicken, Rice & Vegetables (550 cal, 45g protein)
- **Dinner**: Salmon, Sweet Potato & Broccoli (600 cal, 42g protein)
- **Snack**: Protein Shake with Peanut Butter (320 cal, 30g protein)

**Daily Total**: ~1,920 calories, 152g protein, 200g carbs, 52g fat

**Features:**
- Realistic, whole-food based meals
- Variety throughout the week
- Balanced macros for muscle building or fat loss
- Easy to scale up/down based on individual needs

---

## Running All Seeders

### Fresh Database Setup
```bash
# Drop all tables, re-run migrations, and seed
php artisan migrate:fresh --seed --force
```

### Seed Only (Without Migrations)
```bash
php artisan db:seed --force
```

### Run Specific Seeder
```bash
php artisan db:seed --class=WorkoutTemplateSeeder --force
```

---

## Seeder Execution Order

The seeders run in this specific order (defined in `DatabaseSeeder.php`):

1. **UserSeeder** - Creates demo user
2. **ExerciseSeeder** - Creates global exercises
3. **WorkoutTemplateSeeder** - Creates templates (requires user & exercises)
4. **MealPlanSeeder** - Creates meal plan (requires user)

This order respects foreign key dependencies.

---

## Database After Seeding

**Expected Counts:**
- Users: 1
- Exercises: 56
- Workout Templates: 4
- Workout Template Exercises: 24 (6 exercises × 4 templates)
- Meal Plans: 1
- Meals: 28 (7 days × 4 meals)

---

## Customization

### Adding More Exercises
Edit `ExerciseSeeder.php` and add to the `$exercises` array:

```php
['name' => 'Your Exercise', 'category' => 'legs', 'default_rest_sec' => 90],
```

### Modifying Workout Templates
Edit `WorkoutTemplateSeeder.php` and update the `$templates` array.

### Changing Meals
Edit `MealPlanSeeder.php` and modify the `$mealsData` array.

---

## Preventing Duplicates

All seeders use Laravel's `firstOrCreate` method to avoid duplicating data:

- **UserSeeder**: Checks email
- **ExerciseSeeder**: Checks name
- **WorkoutTemplateSeeder**: Checks user_id + name
- **MealPlanSeeder**: Checks user_id + week_start_date

This means you can run the seeders multiple times safely.

---

## Testing the Seeded Data

### Login to the App
```
URL: http://localhost:8000
Email: demo@example.com
Password: password
```

### Verify in Tinker
```bash
php artisan tinker

# Check counts
User::count();                    // 1
Exercise::count();                // 56
WorkoutTemplate::count();         // 4
Meal::count();                    // 28

# View demo user's templates
User::where('email', 'demo@example.com')->first()->workoutTemplates;

# View meal plan
MealPlan::with('meals')->first();
```

---

## Training Split Rationale

The 4-day Upper/Lower split was chosen because:

1. **Optimal Frequency**: Each muscle group trained 2× per week
2. **Recovery**: Built-in rest days for growth
3. **Volume**: Sufficient volume for hypertrophy (10-20 sets per muscle group per week)
4. **Balance**: Equal emphasis on pushing and pulling movements
5. **Flexibility**: Can be adjusted to 3 or 5 days easily
6. **Beginner-Friendly**: Not too complex, easy to follow
7. **Progressive Overload**: Target weights can be increased weekly

---

## Nutritional Rationale

The meal plan was designed with:

1. **Protein Priority**: ~150g+ daily for muscle building
2. **Whole Foods**: Minimal processed foods, nutrient-dense
3. **Meal Timing**: 4 meals for steady energy and protein distribution
4. **Flexibility**: Can add/remove carbs/fats based on goals:
   - **Cutting**: Reduce portions by 20%
   - **Bulking**: Increase portions by 20%
5. **Sustainability**: Realistic meals that are easy to prepare
6. **Variety**: Different meals throughout the week to prevent boredom

---

## Code Quality Features

✅ **Laravel 11 Conventions**: Uses Eloquent models, proper namespacing  
✅ **DRY Principle**: Reusable data structures in arrays  
✅ **Error Handling**: Checks for demo user existence  
✅ **Informative Output**: Console messages during seeding  
✅ **Idempotent**: Safe to run multiple times  
✅ **Clean Code**: Well-organized, commented, readable  

---

## Troubleshooting

### "Demo user not found"
Run `UserSeeder` first:
```bash
php artisan db:seed --class=UserSeeder --force
```

### "Exercise not found"
Ensure `ExerciseSeeder` runs before `WorkoutTemplateSeeder`.

### Foreign Key Errors
Always run migrations before seeding:
```bash
php artisan migrate:fresh --seed --force
```

### Duplicate Entries
The seeders use `firstOrCreate`, so duplicates should not occur. If they do, drop tables and re-seed:
```bash
php artisan migrate:fresh --seed --force
```

---

## Summary

The seeder suite provides:
- **1 demo user** ready to login
- **56 exercises** across all muscle groups  
- **4 workout templates** following proven programming
- **28 meals** for a complete week of eating
- **Realistic data** for testing and demonstration
- **Production-ready** code quality

Perfect for development, testing, and showcasing the app! 💪

---

**Created**: November 2025  
**Version**: 1.0  
**Compatibility**: Laravel 11, MySQL/SQLite

