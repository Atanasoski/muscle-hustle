# Muscle Hustle API Documentation

This documentation provides complete information about all API resources and endpoints for AI applications to understand and interact with the Muscle Hustle API.

## Base URL

All API endpoints require authentication via Laravel Sanctum. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Authentication

Before accessing exercise and workout endpoints, authenticate:
- `POST /api/register` - Register a new user
- `POST /api/login` - Login and receive authentication token
- `POST /api/logout` - Logout (requires auth)

---

## User Management

User profile management endpoints:
- `GET /api/user` - Get current authenticated user (requires auth)
- `PUT/PATCH /api/user` - Update authenticated user's profile (requires auth)

---

## User Profile Resource

### Overview
User profiles contain fitness-related information separate from the main user account. Each user can have one profile with fitness goals, physical attributes, and training preferences.

### API Endpoints

#### Get Current User (with Profile)
```
GET /api/user
```

**Response Structure**:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "profile_photo": "profile-photos/abc123.jpg",
    "profile": {
      "fitness_goal": "muscle_gain",
      "age": 30,
      "gender": "male",
      "height": 180,
      "weight": "75.50",
      "training_experience": "intermediate",
      "training_days_per_week": 4,
      "workout_duration_minutes": 60
    },
    "partner": { /* PartnerResource */ },
    "email_verified_at": "2025-01-01T00:00:00.000000Z",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

**Note**: The `profile` object will be `null` if the user hasn't created a profile yet.

#### Update User Profile
```
PUT/PATCH /api/user
```

**Request Body** (all fields optional):
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "profile_photo": "<file>",
  "fitness_goal": "muscle_gain",
  "age": 30,
  "gender": "male",
  "height": 180,
  "weight": 75.5,
  "training_experience": "intermediate",
  "training_days_per_week": 4,
  "workout_duration_minutes": 60
}
```

**Validation Rules**:
- `name`: sometimes required, string, max 255 characters
- `email`: sometimes required, string, lowercase, email, max 255, unique (except current user)
- `profile_photo`: nullable, image file, mimes: jpeg,png,jpg,gif, max 2048KB
- `fitness_goal`: nullable, enum: `fat_loss`, `muscle_gain`, `strength`, `general_fitness`
- `age`: nullable, integer, min 1, max 150
- `gender`: nullable, enum: `male`, `female`, `other`
- `height`: nullable, integer, min 50, max 300 (in cm)
- `weight`: nullable, numeric, min 1, max 500 (in kg)
- `training_experience`: nullable, enum: `beginner`, `intermediate`, `advanced`
- `training_days_per_week`: nullable, integer, min 1, max 7
- `workout_duration_minutes`: nullable, integer, min 1, max 600

**Response**:
```json
{
  "message": "Profile updated successfully",
  "user": { /* UserResource with updated profile */ }
}
```

**Note**: 
- User fields (name, email, profile_photo) update the user record
- Fitness profile fields create or update the user's profile record
- You can update user fields and profile fields in the same request
- Profile fields are stored in a separate `user_profiles` table

### User Resource Structure

```typescript
interface UserResource {
  id: number;
  name: string;
  email: string;
  profile_photo: string | null;
  profile: UserProfileResource | null;
  partner: PartnerResource | null;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

interface UserProfileResource {
  fitness_goal: string | null;  // "fat_loss" | "muscle_gain" | "strength" | "general_fitness"
  age: number | null;
  gender: string | null;  // "male" | "female" | "other"
  height: number | null;  // in cm
  weight: string | null;  // in kg, decimal as string
  training_experience: string | null;  // "beginner" | "intermediate" | "advanced"
  training_days_per_week: number | null;  // 1-7
  workout_duration_minutes: number | null;  // in minutes
}
```

---

## Exercise Resource

### Overview
Exercises represent individual workout movements (e.g., "Bench Press", "Squat"). Exercises can be:
- **Global**: Available to all users (`user_id` is `null`)
- **User-specific**: Created by individual users (`user_id` is set)

### API Endpoints

#### List All Exercises
```
GET /api/exercises
```

**Response**: Returns all global exercises plus the authenticated user's custom exercises.

**Response Structure**:
```json
{
  "data": [
    {
      "id": 1,
      "user_id": null,
      "category": {
        "id": 1,
        "type": "workout",
        "name": "Chest",
        "slug": "chest",
        "display_order": 1,
        "icon": "chest-icon",
        "color": "#FF5733",
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
      },
      "name": "Bench Press",
      "image_url": "https://example.com/bench-press.jpg",
      "default_rest_sec": 90,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### Get Single Exercise
```
GET /api/exercises/{id}
```

**Authorization**: Can view global exercises or own exercises only.

**Response Structure**: Same as single exercise object in list response.

#### Create Exercise
```
POST /api/exercises
```

**Request Body**:
```json
{
  "name": "Custom Exercise",
  "category_id": 1,
  "image_url": "https://example.com/image.jpg",
  "default_rest_sec": 120
}
```

**Validation Rules**:
- `name`: required, string, max 255 characters
- `category_id`: required, must exist in categories table, must be a workout category type
- `image_url`: nullable, string, valid URL, max 255 characters
- `default_rest_sec`: nullable, integer, minimum 0 (defaults to 90 if not provided)

**Response** (201 Created):
```json
{
  "message": "Exercise created successfully",
  "data": {
    "id": 10,
    "user_id": 1,
    "category": { /* CategoryResource */ },
    "name": "Custom Exercise",
    "image_url": "https://example.com/image.jpg",
    "default_rest_sec": 120,
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

#### Update Exercise
```
PUT/PATCH /api/exercises/{id}
```

**Authorization**: Can only update own exercises (not global ones).

**Request Body**: Same as create, all fields optional except validation rules apply.

**Response**:
```json
{
  "message": "Exercise updated successfully",
  "data": { /* ExerciseResource */ }
}
```

#### Delete Exercise
```
DELETE /api/exercises/{id}
```

**Authorization**: Can only delete own exercises (not global ones).

**Response**:
```json
{
  "message": "Exercise deleted successfully"
}
```

### Exercise Resource Structure

```typescript
interface ExerciseResource {
  id: number;
  user_id: number | null;  // null for global exercises
  category: CategoryResource | null;  // Only present if relationship loaded
  name: string;
  image_url: string | null;
  default_rest_sec: number;
  created_at: string;  // ISO 8601 datetime
  updated_at: string;  // ISO 8601 datetime
}
```

### Category Resource Structure

```typescript
interface CategoryResource {
  id: number;
  type: string;  // Enum: "workout" or other types
  name: string;
  slug: string;
  display_order: number;
  icon: string | null;
  color: string | null;
  created_at: string;
  updated_at: string;
}
```

---

## Workout Template Resource

### Overview
Workout Templates are pre-defined workout plans that contain multiple exercises with specific targets (sets, reps, weight, rest). Each template belongs to a user and can optionally be assigned to a specific day of the week.

### API Endpoints

#### List All Workout Templates
```
GET /api/workout-templates
```

**Response**: Returns all workout templates for the authenticated user, with exercises and categories loaded.

**Response Structure**:
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Push Day",
      "description": "Chest, shoulders, and triceps",
      "day_of_week": 1,
      "exercises": [
        {
          "id": 1,
          "name": "Bench Press",
          "image_url": "https://example.com/bench-press.jpg",
          "default_rest_sec": 90,
          "category": {
            "id": 1,
            "type": "workout",
            "name": "Chest",
            "slug": "chest",
            "display_order": 1,
            "icon": "chest-icon",
            "color": "#FF5733",
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
          },
          "pivot": {
            "order": 1,
            "target_sets": 4,
            "target_reps": 8,
            "target_weight": "80.00",
            "rest_seconds": 90
          }
        }
      ],
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### Get Single Workout Template
```
GET /api/workout-templates/{id}
```

**Authorization**: Can only view own workout templates.

**Response Structure**: Same as single template object in list response.

#### Create Workout Template
```
POST /api/workout-templates
```

**Request Body**:
```json
{
  "name": "Pull Day",
  "description": "Back and biceps workout",
  "day_of_week": 2
}
```

**Validation Rules**:
- `name`: required, string, max 255 characters
- `description`: nullable, string
- `day_of_week`: nullable, integer, min 0, max 6
  - 0 = Sunday, 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Saturday

**Response** (201 Created):
```json
{
  "message": "Workout template created successfully",
  "data": {
    "id": 2,
    "user_id": 1,
    "name": "Pull Day",
    "description": "Back and biceps workout",
    "day_of_week": 2,
    "exercises": [],
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

#### Update Workout Template
```
PUT/PATCH /api/workout-templates/{id}
```

**Authorization**: Can only update own workout templates.

**Request Body**: Same as create, all fields optional except validation rules apply.

**Response**:
```json
{
  "message": "Workout template updated successfully",
  "data": { /* WorkoutTemplateResource */ }
}
```

#### Delete Workout Template
```
DELETE /api/workout-templates/{id}
```

**Authorization**: Can only delete own workout templates.

**Response**:
```json
{
  "message": "Workout template deleted successfully"
}
```

#### Add Exercise to Workout Template
```
POST /api/workout-templates/{id}/exercises
```

**Authorization**: Can only add exercises to own workout templates.

**Request Body**:
```json
{
  "exercise_id": 1,
  "target_sets": 4,
  "target_reps": 8,
  "target_weight": 80.00,
  "rest_seconds": 90
}
```

**Validation Rules**:
- `exercise_id`: required, must exist in workout_exercises table
- `target_sets`: nullable, integer, minimum 1
- `target_reps`: nullable, integer, minimum 1
- `target_weight`: nullable, numeric, minimum 0
- `rest_seconds`: nullable, integer, minimum 0

**Response** (201 Created):
```json
{
  "message": "Exercise added successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Push Day",
    "description": "Chest, shoulders, and triceps",
    "day_of_week": 1,
    "exercises": [
      {
        "id": 1,
        "name": "Bench Press",
        "image_url": "https://example.com/bench-press.jpg",
        "default_rest_sec": 90,
        "category": { /* CategoryResource */ },
        "pivot": {
          "order": 1,
          "target_sets": 4,
          "target_reps": 8,
          "target_weight": "80.00",
          "rest_seconds": 90
        }
      }
    ],
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

**Note**: The exercise is automatically added at the end of the workout (highest order + 1). Use the update order endpoint to reorder exercises.

#### Remove Exercise from Workout Template
```
DELETE /api/workout-templates/{id}/exercises/{exerciseId}
```

**Authorization**: Can only remove exercises from own workout templates.

**Parameters**:
- `{id}`: Workout template ID
- `{exerciseId}`: WorkoutTemplateExercise ID (pivot table record ID, not the exercise ID)

**Response**:
```json
{
  "message": "Exercise removed successfully",
  "data": { /* WorkoutTemplateResource with updated exercises list */ }
}
```

#### Update Exercise in Workout Template
```
PUT /api/workout-templates/{id}/exercises/{exerciseId}
```

**Authorization**: Can only update exercises in own workout templates.

**Request Body**:
```json
{
  "exercise_id": 2,
  "target_sets": 5,
  "target_reps": 10,
  "target_weight": 85.50,
  "rest_seconds": 120
}
```

**Validation Rules**:
- `exercise_id`: sometimes required (if provided, must exist in workout_exercises table)
- `target_sets`: nullable, integer, minimum 1
- `target_reps`: nullable, integer, minimum 1
- `target_weight`: nullable, numeric, minimum 0
- `rest_seconds`: nullable, integer, minimum 0

**Response**:
```json
{
  "message": "Exercise updated successfully",
  "data": { /* WorkoutTemplateResource with updated exercise */ }
}
```

**Note**: All fields are optional. Only include fields you want to update.

#### Update Exercise Order in Workout Template
```
POST /api/workout-templates/{id}/order
```

**Authorization**: Can only update order of exercises in own workout templates.

**Request Body**:
```json
{
  "order": [3, 1, 2, 4]
}
```

**Validation Rules**:
- `order`: required, array of integers
- `order.*`: required, integer (must be valid WorkoutTemplateExercise IDs belonging to this template)

**Response**:
```json
{
  "message": "Order updated successfully",
  "data": { /* WorkoutTemplateResource with reordered exercises */ }
}
```

**Note**: The array should contain all WorkoutTemplateExercise IDs for this template in the desired order. The order will be set based on the array index (0 = first, 1 = second, etc.).

### Workout Template Resource Structure

```typescript
interface WorkoutTemplateResource {
  id: number;
  user_id: number;
  name: string;
  description: string | null;
  day_of_week: number | null;  // 0-6 (Sunday-Saturday)
  exercises: WorkoutTemplateExercise[] | null;  // Only present if relationship loaded
  created_at: string;
  updated_at: string;
}

interface WorkoutTemplateExercise {
  id: number;  // Exercise ID
  name: string;
  image_url: string | null;
  default_rest_sec: number;
  category: CategoryResource | null;
  pivot: {
    order: number;  // Exercise order in the workout
    target_sets: number;
    target_reps: number;
    target_weight: string;  // Decimal as string (2 decimal places)
    rest_seconds: number;
  };
}
```

---

## API Resources Overview

The Muscle Hustle API uses Laravel API Resources to format all responses. All resources are located in `App\Http\Resources\Api\` namespace. The following resources are available:

### Available Resources

1. **UserResource** - User account information
2. **UserProfileResource** - User fitness profile data
3. **ExerciseResource** - Exercise information
4. **CategoryResource** - Exercise/workout categories
5. **WorkoutTemplateResource** - Workout template plans
6. **PartnerResource** - Partner/brand information
7. **PartnerVisualIdentityResource** - Partner visual branding

### Resource Usage

All API endpoints use these resources to ensure consistent response formatting. Resources automatically handle:
- Conditional loading of relationships (using `whenLoaded()`)
- Proper data transformation
- Type casting and formatting
- Null value handling

---

## Partner Resource

### Overview
Partners represent brands or organizations that can customize the application's visual identity. Partners have a visual identity that includes colors, fonts, logos, and styling options.

### Partner Resource Structure

```typescript
interface PartnerResource {
  id: number;
  name: string;
  slug: string;
  domain: string | null;
  is_active: boolean;
  identity: PartnerVisualIdentityResource | null;  // Only present if relationship loaded
  users: UserResource[] | null;  // Only present if relationship loaded
  created_at: string;
  updated_at: string;
}

interface PartnerVisualIdentityResource {
  primary_color: string | null;
  secondary_color: string | null;
  logo: string | null;
  font_family: string | null;
  background_color: string | null;
  card_background_color: string | null;
  text_primary_color: string | null;
  text_secondary_color: string | null;
  text_on_primary_color: string | null;
  success_color: string | null;
  warning_color: string | null;
  danger_color: string | null;
  accent_color: string | null;
  border_color: string | null;
  background_pattern: string | null;
}
```

**Note**: Partner endpoints are not currently exposed in the public API routes, but the resources are available for internal use and future API expansion.

---

## Relationships

### Exercise Relationships
- **Category**: Each exercise belongs to one category (workout type only)
- **User**: Exercises can belong to a user (nullable for global exercises)
- **WorkoutTemplateExercises**: Exercises can be part of multiple workout templates

### Workout Template Relationships
- **User**: Each template belongs to one user
- **Exercises**: Many-to-many relationship through `workout_template_exercises` pivot table
- **WorkoutSessions**: Templates can have multiple workout sessions (tracked workouts)

### Pivot Table: workout_template_exercises
The pivot table connects exercises to workout templates with additional metadata:
- `workout_template_id`: Foreign key to workout template
- `exercise_id`: Foreign key to exercise
- `order`: Integer - determines exercise order in the workout
- `target_sets`: Integer - target number of sets
- `target_reps`: Integer - target number of reps per set
- `target_weight`: Decimal(2) - target weight in appropriate units
- `rest_seconds`: Integer - rest time between sets for this exercise in this template

---

## Error Responses

All endpoints may return the following error responses:

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized"
}
```
or
```json
{
  "message": "Unauthorized. You can only edit your own exercises."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "category_id": ["The selected category must be a workout category."],
    "order": ["Some exercise IDs do not belong to this template"]
  }
}
```

### 404 Not Found
```json
{
  "message": "No query results for model [App\\Models\\Exercise] {id}"
}
```

---

## Usage Examples for AI Applications

### Example 1: Create a Complete Workout Template
1. First, get available exercises: `GET /api/exercises`
2. Create workout template: `POST /api/workout-templates` with name, description, day_of_week
3. Add exercises to template: `POST /api/workout-templates/{id}/exercises` for each exercise
4. Optionally reorder exercises: `POST /api/workout-templates/{id}/order` with array of exercise IDs

### Example 2: Create a Custom Exercise
```json
POST /api/exercises
{
  "name": "Dumbbell Flyes",
  "category_id": 1,
  "image_url": "https://example.com/dumbbell-flyes.jpg",
  "default_rest_sec": 60
}
```

### Example 3: Get User's Workout Plan for the Week
1. `GET /api/workout-templates` - Returns all templates with exercises
2. Filter by `day_of_week` to get templates for specific days
3. Each template includes full exercise details with targets

### Example 4: Add Multiple Exercises to a Template
```json
POST /api/workout-templates/1/exercises
{
  "exercise_id": 1,
  "target_sets": 4,
  "target_reps": 8,
  "target_weight": 80.00,
  "rest_seconds": 90
}

POST /api/workout-templates/1/exercises
{
  "exercise_id": 5,
  "target_sets": 3,
  "target_reps": 12,
  "target_weight": 25.00,
  "rest_seconds": 60
}
```

### Example 5: Reorder Exercises in a Template
After adding exercises, you can reorder them:
```json
POST /api/workout-templates/1/order
{
  "order": [2, 1, 3]
}
```
This sets the order based on WorkoutTemplateExercise IDs (not exercise IDs).

---

## Important Notes for AI Integration

1. **Authentication Required**: All exercise and workout endpoints require valid Sanctum token
2. **User Isolation**: Users can only see/modify their own workout templates and custom exercises
3. **Global Exercises**: Exercises with `user_id: null` are available to all users but cannot be modified
4. **Category Validation**: When creating exercises, the category must be of type "workout"
5. **Day of Week**: Use integer 0-6 (Sunday=0, Monday=1, ..., Saturday=6)
6. **Exercise Order**: In workout templates, exercises are ordered by the `pivot.order` field
7. **Default Values**: `default_rest_sec` defaults to 90 seconds if not provided when creating exercises
8. **Weight Format**: Target weights are stored as decimals with 2 decimal places, returned as strings in JSON
9. **Exercise Management**: When removing or updating exercises in templates, use the WorkoutTemplateExercise ID (pivot record ID), not the exercise ID
10. **Auto-Ordering**: New exercises are automatically added at the end. Use the order endpoint to rearrange them

---

## Quick Reference

### Authentication Endpoints
- `POST /api/register` - Register a new user
- `POST /api/login` - Login and receive authentication token
- `POST /api/logout` - Logout (requires auth)
- `GET /api/user` - Get current authenticated user (requires auth)
- `PUT/PATCH /api/user` - Update user profile (requires auth)

### Exercise Endpoints
- `GET /api/exercises` - List exercises
- `POST /api/exercises` - Create exercise
- `GET /api/exercises/{id}` - Get exercise
- `PUT/PATCH /api/exercises/{id}` - Update exercise
- `DELETE /api/exercises/{id}` - Delete exercise

### Workout Template Endpoints
- `GET /api/workout-templates` - List templates
- `POST /api/workout-templates` - Create template
- `GET /api/workout-templates/{id}` - Get template
- `PUT/PATCH /api/workout-templates/{id}` - Update template
- `DELETE /api/workout-templates/{id}` - Delete template

### Workout Template Exercise Management Endpoints
- `POST /api/workout-templates/{id}/exercises` - Add exercise to template
- `DELETE /api/workout-templates/{id}/exercises/{exerciseId}` - Remove exercise from template
- `PUT /api/workout-templates/{id}/exercises/{exerciseId}` - Update exercise in template
- `POST /api/workout-templates/{id}/order` - Update exercise order

### Required Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Complete Resource Type Definitions

For TypeScript/JavaScript integration, here are all resource type definitions:

```typescript
// User Resources
interface UserResource {
  id: number;
  name: string;
  email: string;
  profile_photo: string | null;
  profile: UserProfileResource | null;
  partner: {
    id: number;
    name: string;
    slug: string;
    visual_identity: PartnerVisualIdentityResource | null;
  } | null;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

interface UserProfileResource {
  fitness_goal: string | null;  // "fat_loss" | "muscle_gain" | "strength" | "general_fitness"
  age: number | null;
  gender: string | null;  // "male" | "female" | "other"
  height: number | null;  // in cm
  weight: string | null;  // in kg, decimal as string
  training_experience: string | null;  // "beginner" | "intermediate" | "advanced"
  training_days_per_week: number | null;  // 1-7
  workout_duration_minutes: number | null;  // in minutes
}

// Exercise Resources
interface ExerciseResource {
  id: number;
  user_id: number | null;  // null for global exercises
  category: CategoryResource | null;  // Only present if relationship loaded
  name: string;
  image_url: string | null;
  default_rest_sec: number;
  created_at: string;  // ISO 8601 datetime
  updated_at: string;  // ISO 8601 datetime
}

interface CategoryResource {
  id: number;
  type: string;  // Enum: "workout" or other types
  name: string;
  slug: string;
  display_order: number;
  icon: string | null;
  color: string | null;
  created_at: string;
  updated_at: string;
}

// Workout Template Resources
interface WorkoutTemplateResource {
  id: number;
  user_id: number;
  name: string;
  description: string | null;
  day_of_week: number | null;  // 0-6 (Sunday-Saturday)
  exercises: WorkoutTemplateExercise[] | null;  // Only present if relationship loaded
  created_at: string;
  updated_at: string;
}

interface WorkoutTemplateExercise {
  id: number;  // Exercise ID
  name: string;
  image_url: string | null;
  default_rest_sec: number;
  category: CategoryResource | null;
  pivot: {
    id: number;  // WorkoutTemplateExercise ID (pivot record ID)
    order: number;  // Exercise order in the workout
    target_sets: number | null;
    target_reps: number | null;
    target_weight: string | null;  // Decimal as string (2 decimal places)
    rest_seconds: number | null;
  };
}

// Partner Resources
interface PartnerResource {
  id: number;
  name: string;
  slug: string;
  domain: string | null;
  is_active: boolean;
  identity: PartnerVisualIdentityResource | null;  // Only present if relationship loaded
  users: UserResource[] | null;  // Only present if relationship loaded
  created_at: string;
  updated_at: string;
}

interface PartnerVisualIdentityResource {
  primary_color: string | null;
  secondary_color: string | null;
  logo: string | null;
  font_family: string | null;
  background_color: string | null;
  card_background_color: string | null;
  text_primary_color: string | null;
  text_secondary_color: string | null;
  text_on_primary_color: string | null;
  success_color: string | null;
  warning_color: string | null;
  danger_color: string | null;
  accent_color: string | null;
  border_color: string | null;
  background_pattern: string | null;
}
```

---

## Resource Implementation Details

### Resource Files Location
All API resources are located in: `app/Http/Resources/Api/`

### Resource Files
- `UserResource.php` - Formats user data with profile and partner relationships
- `UserProfileResource.php` - Formats user fitness profile data
- `ExerciseResource.php` - Formats exercise data with category relationship
- `CategoryResource.php` - Formats category data
- `WorkoutTemplateResource.php` - Formats workout template data with exercises
- `PartnerResource.php` - Formats partner data with identity and users
- `PartnerVisualIdentityResource.php` - Formats partner visual branding data

### Resource Features
- **Conditional Loading**: Resources use `whenLoaded()` to only include relationships when they're eager loaded
- **Type Safety**: All resources use proper type hints and return type declarations
- **Consistent Formatting**: All resources follow the same structure and naming conventions
- **Null Handling**: Resources properly handle null values for optional fields and relationships
