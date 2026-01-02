# Frontend Migration Guide: Workout Plan System

## Overview

The workout template system has been restructured to introduce a **Plan** layer between Users and WorkoutTemplates. This allows users to organize their workout templates into different plans (e.g., "Bulking Plan", "Cutting Plan", "Maintenance Plan") and activate/deactivate them as needed.

## Key Changes

### 1. New Entity: Plan
- Plans belong to Users (one user can have multiple plans)
- Plans have an `is_active` boolean property
- WorkoutTemplates now belong to Plans instead of directly to Users

### 2. WorkoutTemplate Changes
- **Removed**: `user_id` field
- **Added**: `plan_id` field (required)
- WorkoutTemplates are now scoped through Plans

## API Changes

### New Endpoints: Plans CRUD

#### List All Plans
```
GET /api/plans
```
**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "My Training Plan",
      "description": "Main workout plan for strength and muscle building",
      "is_active": true,
      "workout_templates": [...], // Only if loaded
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### Create Plan
```
POST /api/plans
Content-Type: application/json

{
  "name": "My Training Plan",
  "description": "Main workout plan",
  "is_active": true  // optional, defaults to false
}
```

#### Get Plan
```
GET /api/plans/{id}
```

#### Update Plan
```
PUT /api/plans/{id}
Content-Type: application/json

{
  "name": "Updated Plan Name",
  "description": "Updated description",
  "is_active": false
}
```

#### Delete Plan
```
DELETE /api/plans/{id}
```
**Note**: Deleting a plan will cascade delete all associated workout templates.

### Updated Endpoints: Workout Templates

#### Create Workout Template (BREAKING CHANGE)
```
POST /api/workout-templates
Content-Type: application/json

{
  "plan_id": 1,  // REQUIRED - was not needed before
  "name": "Day 1 - Monday",
  "description": "Push focus with quads",
  "day_of_week": 0  // optional
}
```

**Before:**
```json
{
  "name": "Day 1 - Monday",
  "description": "Push focus with quads",
  "day_of_week": 0
}
```

**After:**
```json
{
  "plan_id": 1,  // MUST be included
  "name": "Day 1 - Monday",
  "description": "Push focus with quads",
  "day_of_week": 0
}
```

#### Update Workout Template (BREAKING CHANGE)
```
PUT /api/workout-templates/{id}
Content-Type: application/json

{
  "plan_id": 2,  // OPTIONAL - can change template's plan
  "name": "Updated Name",
  "description": "Updated description",
  "day_of_week": 1
}
```

#### Workout Template Response (BREAKING CHANGE)

**Before:**
```json
{
  "id": 1,
  "user_id": 1,  // REMOVED
  "name": "Day 1 - Monday",
  "description": "Push focus with quads",
  "day_of_week": 0,
  "exercises": [...],
  "created_at": "...",
  "updated_at": "..."
}
```

**After:**
```json
{
  "id": 1,
  "plan_id": 1,  // NEW - replaces user_id
  "name": "Day 1 - Monday",
  "description": "Push focus with quads",
  "day_of_week": 0,
  "plan": {  // NEW - included when relationship is loaded
    "id": 1,
    "user_id": 1,
    "name": "My Training Plan",
    "description": "Main workout plan",
    "is_active": true
  },
  "exercises": [...],
  "created_at": "...",
  "updated_at": "..."
}
```

## Frontend Migration Steps

### Step 1: Update TypeScript/Type Definitions

#### Update WorkoutTemplate Interface
```typescript
// BEFORE
interface WorkoutTemplate {
  id: number;
  user_id: number;  // REMOVE
  name: string;
  description: string | null;
  day_of_week: number | null;
  exercises: WorkoutTemplateExercise[] | null;
  created_at: string;
  updated_at: string;
}

// AFTER
interface WorkoutTemplate {
  id: number;
  plan_id: number;  // NEW - replaces user_id
  name: string;
  description: string | null;
  day_of_week: number | null;
  plan?: Plan;  // NEW - optional, included when loaded
  exercises: WorkoutTemplateExercise[] | null;
  created_at: string;
  updated_at: string;
}
```

#### Add Plan Interface
```typescript
interface Plan {
  id: number;
  user_id: number;
  name: string;
  description: string | null;
  is_active: boolean;
  workout_templates?: WorkoutTemplate[];  // Optional, included when loaded
  created_at: string;
  updated_at: string;
}
```

### Step 2: Update Workout Template Creation Forms

**Before:**
```typescript
const createTemplate = async (data: {
  name: string;
  description?: string;
  day_of_week?: number;
}) => {
  await api.post('/workout-templates', data);
};
```

**After:**
```typescript
const createTemplate = async (data: {
  plan_id: number;  // REQUIRED
  name: string;
  description?: string;
  day_of_week?: number;
}) => {
  await api.post('/workout-templates', data);
};
```

**UI Changes Needed:**
- Add a plan selector dropdown to the workout template creation form
- Fetch available plans first: `GET /api/plans`
- Require user to select a plan before creating a template
- Consider showing only active plans, or all plans with active status indicator

### Step 3: Update Workout Template Update Forms

**Before:**
```typescript
const updateTemplate = async (id: number, data: {
  name: string;
  description?: string;
  day_of_week?: number;
}) => {
  await api.put(`/workout-templates/${id}`, data);
};
```

**After:**
```typescript
const updateTemplate = async (id: number, data: {
  plan_id?: number;  // OPTIONAL - can change plan
  name: string;
  description?: string;
  day_of_week?: number;
}) => {
  await api.put(`/workout-templates/${id}`, data);
};
```

**UI Changes Needed:**
- Add plan selector to edit form (optional, for moving template to different plan)
- Pre-populate with current `plan_id` from template data

### Step 4: Update Workout Template List/Display

**Before:**
```typescript
// Templates were implicitly scoped to current user
const templates = await api.get('/workout-templates');
```

**After:**
```typescript
// Templates are now scoped through plans
// The API still returns all templates for the authenticated user,
// but they're now organized by plan
const templates = await api.get('/workout-templates');

// Or fetch by plan:
const plan = await api.get(`/plans/${planId}`);
// plan.workout_templates contains templates for that plan
```

**UI Changes Needed:**
- Consider grouping templates by plan in the UI
- Show plan name/badge next to each template
- Add filter to show templates from specific plan
- Display active plan indicator

### Step 5: Add Plan Management UI

#### Plan List View
```typescript
const plans = await api.get('/plans');
// Display list of plans with:
// - Name
// - Description
// - Active status badge
// - Number of templates in plan
// - Actions: Edit, Delete, Activate/Deactivate
```

#### Plan Creation Form
```typescript
const createPlan = async (data: {
  name: string;
  description?: string;
  is_active?: boolean;
}) => {
  await api.post('/plans', data);
};
```

#### Plan Activation/Deactivation
```typescript
const togglePlanActive = async (planId: number, isActive: boolean) => {
  await api.put(`/plans/${planId}`, { is_active: isActive });
};
```

### Step 6: Update Workflow

#### Recommended User Flow

1. **Onboarding/First Use:**
   - Check if user has any plans: `GET /api/plans`
   - If no plans exist, prompt user to create their first plan
   - Set the first plan as active by default

2. **Creating Workout Templates:**
   - User must select a plan before creating a template
   - If only one plan exists, auto-select it
   - If multiple plans exist, show plan selector
   - Consider defaulting to the active plan

3. **Viewing Templates:**
   - Option 1: Show all templates in a flat list (current behavior, but with plan info)
   - Option 2: Group templates by plan (recommended)
   - Show plan name/badge for each template
   - Allow filtering by plan

4. **Plan Management:**
   - Add "Plans" section to navigation
   - Allow users to:
     - Create new plans
     - Edit plan details
     - Activate/deactivate plans
     - Delete plans (with confirmation, as it deletes templates)
     - View templates within each plan

### Step 7: Handle Edge Cases

#### No Plans Exist
```typescript
const plans = await api.get('/plans');
if (plans.data.length === 0) {
  // Prompt user to create first plan
  // Or auto-create a default plan
  await api.post('/plans', {
    name: 'My Training Plan',
    is_active: true
  });
}
```

#### No Active Plan
```typescript
const activePlan = plans.data.find(p => p.is_active);
if (!activePlan) {
  // Prompt user to activate a plan
  // Or auto-activate the first plan
}
```

#### Deleting a Plan
```typescript
// Warn user that all templates in the plan will be deleted
const deletePlan = async (planId: number) => {
  const confirmed = confirm(
    'Deleting this plan will also delete all workout templates in it. Continue?'
  );
  if (confirmed) {
    await api.delete(`/plans/${planId}`);
  }
};
```

## Migration Checklist

- [ ] Update TypeScript interfaces for `WorkoutTemplate` and add `Plan` interface
- [ ] Update workout template creation form to include `plan_id` selector
- [ ] Update workout template update form to optionally include `plan_id`
- [ ] Update workout template list to show plan information
- [ ] Remove any references to `user_id` on workout templates
- [ ] Add plan management UI (list, create, edit, delete)
- [ ] Add plan activation/deactivation functionality
- [ ] Update API calls to include `plan_id` when creating templates
- [ ] Handle case when user has no plans
- [ ] Handle case when user has no active plan
- [ ] Update any filters or queries that used `user_id` on templates
- [ ] Test template creation with plan selection
- [ ] Test template updates with plan changes
- [ ] Test plan deletion and cascade behavior

## Example Implementation

### Plan Selector Component
```typescript
const PlanSelector = ({ value, onChange, onlyActive = false }) => {
  const { data: plans } = useQuery('/plans');
  
  const filteredPlans = onlyActive 
    ? plans?.filter(p => p.is_active) 
    : plans;
  
  return (
    <select value={value} onChange={e => onChange(Number(e.target.value))}>
      <option value="">Select a plan</option>
      {filteredPlans?.map(plan => (
        <option key={plan.id} value={plan.id}>
          {plan.name} {plan.is_active && '(Active)'}
        </option>
      ))}
    </select>
  );
};
```

### Template Creation with Plan
```typescript
const CreateTemplateForm = () => {
  const [planId, setPlanId] = useState<number | null>(null);
  const [name, setName] = useState('');
  
  const { data: plans } = useQuery('/plans');
  const activePlan = plans?.find(p => p.is_active);
  
  useEffect(() => {
    // Auto-select active plan if exists
    if (activePlan) {
      setPlanId(activePlan.id);
    }
  }, [activePlan]);
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!planId) {
      alert('Please select a plan');
      return;
    }
    
    await api.post('/workout-templates', {
      plan_id: planId,
      name,
      // ... other fields
    });
  };
  
  return (
    <form onSubmit={handleSubmit}>
      <PlanSelector 
        value={planId} 
        onChange={setPlanId}
        required
      />
      <input 
        value={name} 
        onChange={e => setName(e.target.value)}
        placeholder="Template name"
        required
      />
      {/* ... other fields */}
    </form>
  );
};
```

## API Response Examples

### Get Plans with Templates
```
GET /api/plans?with=templates
```
Response includes `workout_templates` array in each plan.

### Get Plan Details
```
GET /api/plans/1
```
Returns plan with all templates loaded.

### Get Templates (unchanged endpoint, changed response)
```
GET /api/workout-templates
```
Still returns all templates for the user, but now includes `plan_id` instead of `user_id`.

## Notes

- All existing workout template endpoints remain functional
- Authorization is now based on plan ownership (user owns the plan)
- The API automatically filters templates to only show those from plans owned by the authenticated user
- Plan deletion cascades to workout templates (all templates in the plan are deleted)
- Only one plan can be active per user (though the API doesn't enforce this - you may want to add frontend logic)

## Testing Recommendations

1. Test creating a plan
2. Test creating a workout template with a plan
3. Test updating a workout template's plan
4. Test deleting a plan (verify templates are deleted)
5. Test activating/deactivating plans
6. Test filtering templates by plan
7. Test edge cases (no plans, no active plan)

