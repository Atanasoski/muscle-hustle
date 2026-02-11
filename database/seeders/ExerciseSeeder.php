<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use App\Services\MuscleGroupImageService;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function __construct(
        private MuscleGroupImageService $muscleImageService
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories indexed by slug for easy lookup
        $categories = Category::pluck('id', 'slug');

        // Get all muscle groups indexed by name for easy lookup
        $muscleGroups = MuscleGroup::pluck('id', 'name');

        // Exercise data with category and muscle group mappings
        // Format: name, category_slug, default_rest_sec, primary_muscles[], secondary_muscles[]
        // Chest exercises (expanded)
        $exercises = [

            // =========================
            // CHEST
            // =========================

            [
                'name' => 'Barbell Bench Press',

                'default_rest_sec' => 120,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Lie on a flat bench, grip bar slightly wider than shoulders, lower to mid-chest with control, press up to lockout while keeping shoulder blades retracted.',
            ],
            [
                'name' => 'Dumbbell Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Lie on a flat bench with dumbbells at chest level, press up and slightly inward, then lower under control to a comfortable stretch.',
            ],
            [
                'name' => 'Incline Barbell Bench Press',

                'default_rest_sec' => 120,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Set bench to an incline, lower bar to upper chest/clavicle area with elbows slightly tucked, press up while keeping upper back tight.',
            ],
            [
                'name' => 'Incline Dumbbell Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
                'description' => 'On an incline bench, start dumbbells near upper chest, press up until arms extend, lower slowly keeping wrists stacked over elbows.',
            ],
            [
                'name' => 'Decline Barbell Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps'],
                'description' => 'On a decline bench, unrack bar, lower to lower-chest area, press up while maintaining tight shoulder blades and stable feet/legs.',
            ],
            [
                'name' => 'Decline Dumbbell Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'On a decline bench, press dumbbells from lower chest to lockout, then lower with control while keeping elbows at a safe angle.',
            ],
            [
                'name' => 'Close-Grip Bench Press (Chest Focus)',

                'default_rest_sec' => 90,
                'primary' => ['Chest', 'Triceps'],
                'secondary' => ['Front Delts'],
                'description' => 'On a flat bench, grip bar just inside shoulder width, lower to lower chest, press up keeping elbows closer to the torso.',
            ],

            [
                'name' => 'Machine Chest Press',

                'default_rest_sec' => 75,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Adjust seat so handles align with mid-chest, press handles forward without shrugging, return until chest stretches lightly.',
            ],
            [
                'name' => 'Incline Chest Press Machine',

                'default_rest_sec' => 75,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Set seat so handles align with upper chest, press forward/upward path, pause briefly, return under control.',
            ],
            [
                'name' => 'Decline Chest Press Machine',

                'default_rest_sec' => 75,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Adjust seat for a lower press line, press handles forward, keep shoulder blades back, return to a controlled stretch.',
            ],

            [
                'name' => 'Plate-Loaded Chest Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Set seat and grip, press the handles through full range, avoid locking shoulders forward, lower slowly.',
            ],
            [
                'name' => 'Plate-Loaded Incline Chest Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Press handles on an incline path from upper-chest level, squeeze at top, lower to a comfortable stretch.',
            ],
            [
                'name' => 'Plate-Loaded Decline Chest Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Press handles on a slight downward/forward line, keep chest up, lower under control without bouncing.',
            ],

            [
                'name' => 'Smith Machine Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Lie under smith bar, set bench position so bar tracks to mid-chest, lower to chest, press up keeping wrists stacked.',
            ],
            [
                'name' => 'Smith Machine Incline Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
                'description' => 'On an incline bench, lower smith bar to upper chest, press up while keeping shoulder blades pinned back.',
            ],
            [
                'name' => 'Smith Machine Decline Bench Press',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'On a decline bench, lower smith bar to lower chest, press up smoothly, avoid flaring elbows excessively.',
            ],

            [
                'name' => 'Cable Chest Press',

                'default_rest_sec' => 75,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts', 'Abs'],
                'description' => 'Set cables at chest height, step forward into a staggered stance, press handles forward together, return with control resisting the pull.',
            ],
            [
                'name' => 'Seated Cable Chest Press',

                'default_rest_sec' => 75,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Sit upright with cables behind you, press handles forward, keep ribcage down, return until elbows are slightly behind torso.',
            ],
            [
                'name' => 'Single-Arm Cable Chest Press',

                'default_rest_sec' => 75,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts', 'Abs'],
                'description' => 'Press one cable handle forward while bracing core to resist rotation, pause, then return slowly; repeat other side.',
            ],

            [
                'name' => 'Pec Deck Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
                'description' => 'Set seat so elbows align with mid-chest, bring pads/handles together with a slight bend in elbows, return until chest stretches.',
            ],
            [
                'name' => 'Machine Chest Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
                'description' => 'With a soft elbow bend, sweep arms together in front of chest, squeeze briefly, then open under control.',
            ],

            [
                'name' => 'Cable Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
                'description' => 'Set cables slightly above shoulder height, step forward, bring hands together in an arc, keep elbows softly bent, return to stretch.',
            ],
            [
                'name' => 'Low-to-High Cable Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => [],
                'description' => 'Set cables low, sweep hands upward and inward to upper-chest height, squeeze, then return slowly keeping tension.',
            ],
            [
                'name' => 'High-to-Low Cable Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
                'description' => 'Set cables high, sweep hands down and inward toward lower chest/upper abs, pause, then return with control.',
            ],
            [
                'name' => 'Single-Arm Cable Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Abs'],
                'description' => 'With one handle, perform a fly arc across your body while bracing core to avoid rotation; control the return to stretch.',
            ],
            [
                'name' => 'Single-Arm Cable Chest Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Abs'],
                'description' => 'With one cable handle, perform a fly arc across your body focusing on chest contraction while bracing core to avoid rotation; control the return to stretch.',
            ],

            [
                'name' => 'Dumbbell Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
                'description' => 'Lie on a flat bench, start dumbbells above chest, lower arms in a wide arc with soft elbows until stretched, bring back together.',
            ],
            [
                'name' => 'Incline Dumbbell Fly',

                'default_rest_sec' => 60,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => [],
                'description' => 'On an incline bench, lower dumbbells in a wide arc to a comfortable stretch, then sweep back up to meet above chest.',
            ],
            [
                'name' => 'Dumbbell Pullover (Chest Focus)',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Lats', 'Triceps'],
                'description' => 'Lie on a bench holding one dumbbell, lower it behind head with slightly bent elbows until stretch, pull back over chest.',
            ],

            [
                'name' => 'Push-ups',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts', 'Abs'],
                'description' => 'Hands under shoulders, body straight, lower chest toward floor, press back up while keeping core tight and elbows controlled.',
            ],
            [
                'name' => 'Wide Push-ups',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Front Delts', 'Abs'],
                'description' => 'Set hands wider than shoulders, keep body rigid, lower under control, press up without letting hips sag.',
            ],
            [
                'name' => 'Incline Push-ups',

                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts', 'Abs'],
                'description' => 'Hands on a bench/box, body straight, lower chest to the surface, press up to full extension while bracing core.',
            ],
            [
                'name' => 'Decline Push-ups',

                'default_rest_sec' => 75,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps', 'Abs'],
                'description' => 'Feet elevated, hands on floor, lower chest down with control, press up while keeping hips and ribs stacked.',
            ],
            [
                'name' => 'Dips (Chest)',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'On dip bars, lean slightly forward, lower until shoulders are below elbows comfortably, press up while keeping elbows tracking back.',
            ],
            [
                'name' => 'Assisted Dips (Chest)',

                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
                'description' => 'Use assisted dip machine, keep a slight forward lean for chest emphasis, lower under control, press back to lockout.',
            ],

            // =========================
            // BACK
            // =========================

            [
                'name' => 'Deadlift',

                'default_rest_sec' => 180,
                'primary' => ['Lower Back', 'Glutes', 'Hamstrings'],
                'secondary' => ['Lats', 'Upper Back', 'Quads', 'Forearms', 'Core'],
                'description' => 'Stand with bar over mid-foot, hinge down to grip, brace core, drive through floor to stand tall, lower by hinging back with control.',
            ],
            [
                'name' => 'Romanian Deadlift',

                'default_rest_sec' => 120,
                'primary' => ['Hamstrings', 'Glutes', 'Lower Back'],
                'secondary' => ['Lats', 'Forearms', 'Core'],
                'description' => 'Hold bar at hips, soften knees, hinge hips back keeping bar close to legs, lower until hamstrings stretch, return by driving hips forward.',
            ],
            [
                'name' => 'Rack Pull',

                'default_rest_sec' => 150,
                'primary' => ['Lower Back', 'Upper Back'],
                'secondary' => ['Glutes', 'Hamstrings', 'Forearms', 'Lats', 'Core'],
                'description' => 'Set bar on pins just below/at knees, brace and pull to lockout, squeeze upper back, lower to pins under control.',
            ],
            [
                'name' => 'Trap Bar Deadlift',

                'default_rest_sec' => 180,
                'primary' => ['Lower Back', 'Glutes', 'Hamstrings', 'Quads'],
                'secondary' => ['Lats', 'Upper Back', 'Forearms', 'Core'],
                'description' => 'Stand inside trap bar, grip handles, brace core, drive through floor to stand tall, keep torso more upright than conventional deadlift, lower with control.',
            ],

            [
                'name' => 'Barbell Row',

                'default_rest_sec' => 120,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts', 'Lower Back', 'Core', 'Forearms'],
                'description' => 'Hinge to a flat-back position, pull bar toward lower ribs/upper stomach, pause, lower under control without losing torso angle.',
            ],
            [
                'name' => 'Pendlay Row',

                'default_rest_sec' => 120,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Lower Back', 'Core', 'Forearms'],
                'description' => 'Start with bar on floor each rep, hinge with flat back, pull explosively to torso, return bar to floor and reset.',
            ],
            [
                'name' => 'Dumbbell Row',

                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts', 'Core', 'Forearms'],
                'description' => 'Support one knee/hand on bench, pull dumbbell toward hip, squeeze back, lower slowly to full stretch.',
            ],
            [
                'name' => 'Chest-Supported Dumbbell Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Lie face down on incline bench, row dumbbells toward lower ribs, pause, lower to full stretch while keeping chest on pad.',
            ],

            [
                'name' => 'Seated Cable Row',

                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Sit tall, pull handle to torso while keeping elbows close, squeeze shoulder blades, return with control without rounding forward.',
            ],
            [
                'name' => 'Close-Grip Seated Cable Row',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Upper Back', 'Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Use close neutral handle, pull toward lower ribs, keep elbows tucked, pause and squeeze, return slowly.',
            ],
            [
                'name' => 'Wide-Grip Seated Cable Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back'],
                'secondary' => ['Lats', 'Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Use wide grip, pull toward upper stomach, flare elbows slightly, squeeze upper back, return under control.',
            ],
            [
                'name' => 'Machine Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Set seat/chest pad, pull handles back to torso, squeeze, then return to stretch without shrugging.',
            ],
            [
                'name' => 'Chest-Supported Machine Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Brace chest against pad, row handles back, pause, then return slowly maintaining contact with pad.',
            ],
            [
                'name' => 'Iso-Lateral Row Machine',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Row each handle evenly (or one at a time), drive elbow back, squeeze, return to full stretch.',
            ],
            [
                'name' => 'Plate-Loaded Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'Set chest pad/seat, pull handles toward torso, keep shoulders down, return slowly.',
            ],
            [
                'name' => 'T-Bar Row',

                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Lower Back', 'Forearms', 'Core'],
                'description' => 'Straddle bar, hinge to flat back, pull handle toward chest/upper stomach, pause, lower under control.',
            ],
            [
                'name' => 'Chest-Supported T-Bar Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Rear Delts', 'Forearms'],
                'description' => 'With chest on pad, pull handle toward chest, squeeze, return slowly keeping shoulders down.',
            ],

            [
                'name' => 'Pull-ups',

                'default_rest_sec' => 120,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Core', 'Forearms'],
                'description' => 'Hang from bar, brace core, pull chest toward bar by driving elbows down, lower to full hang with control.',
            ],
            [
                'name' => 'Wide-Grip Pull-ups',

                'default_rest_sec' => 120,
                'primary' => ['Lats'],
                'secondary' => ['Upper Back', 'Biceps', 'Core', 'Forearms'],
                'description' => 'Grip bar wider than shoulders, pull chest toward bar, keep torso stable, lower to full hang with control.',
            ],
            [
                'name' => 'Close-Grip Pull-ups',

                'default_rest_sec' => 120,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Core', 'Forearms'],
                'description' => 'Grip bar closer than shoulders, pull chest toward bar, keep elbows tucked, lower to full hang with control.',
            ],
            [
                'name' => 'Chin-ups',

                'default_rest_sec' => 120,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Core', 'Forearms'],
                'description' => 'Use underhand grip, pull up until chin clears bar, squeeze lats, lower slowly to full extension.',
            ],
            [
                'name' => 'Assisted Pull-ups',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Core', 'Forearms'],
                'description' => 'Use assisted machine/band, pull chest toward bar, pause, lower slowly maintaining control and full range.',
            ],
            [
                'name' => 'Lat Pulldown',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Forearms'],
                'description' => 'Sit with thighs secured, pull bar to upper chest while driving elbows down, pause, return until arms extend.',
            ],
            [
                'name' => 'Wide-Grip Lat Pulldown',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Upper Back', 'Biceps', 'Forearms'],
                'description' => 'Grip wide, pull bar to upper chest, keep torso mostly upright, return slowly to full stretch.',
            ],
            [
                'name' => 'Close-Grip Lat Pulldown',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Forearms'],
                'description' => 'Use close handle, pull to upper chest, keep elbows tucked, squeeze lats, return under control.',
            ],
            [
                'name' => 'Underhand Close-Grip Lat Pulldown',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Forearms'],
                'description' => 'Use close underhand grip, pull bar to upper chest, keep elbows tucked, squeeze lats and biceps, return under control.',
            ],
            [
                'name' => 'Behind-the-Head Lat Pulldown',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Upper Back', 'Biceps', 'Forearms'],
                'description' => 'Pull bar behind head to upper neck/shoulder level, keep torso upright, return slowly to full stretch; use caution with shoulder mobility.',
            ],
            [
                'name' => 'Neutral-Grip Lat Pulldown',

                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Forearms'],
                'description' => 'Use neutral grip, pull handle to upper chest, keep shoulders down, return slowly to full extension.',
            ],
            [
                'name' => 'Single-Arm Lat Pulldown',

                'default_rest_sec' => 75,
                'primary' => ['Lats'],
                'secondary' => ['Upper Back', 'Biceps', 'Core', 'Forearms'],
                'description' => 'Kneel or sit sideways to cable, pull elbow down toward hip, pause, return to stretch; brace core to avoid twisting.',
            ],

            [
                'name' => 'Straight-Arm Cable Pulldown',

                'default_rest_sec' => 60,
                'primary' => ['Lats'],
                'secondary' => ['Core'],
                'description' => 'With straight arms (soft elbows), hinge slightly, pull bar down to thighs, squeeze lats, return slowly.',
            ],
            [
                'name' => 'Cable Pullover',

                'default_rest_sec' => 60,
                'primary' => ['Lats'],
                'secondary' => ['Core'],
                'description' => 'Facing away or toward cable (depending on setup), keep arms slightly bent, sweep handle in an arc to hips, return with control.',
            ],

            [
                'name' => 'Face Pulls',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts', 'Upper Back'],
                'secondary' => [],
                'description' => 'Set rope at face height, pull toward nose/forehead with elbows high, squeeze rear delts, return slowly.',
            ],
            [
                'name' => 'Reverse Cable Flyes',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
                'description' => 'With cables crossed, open arms out to the sides keeping soft elbows, squeeze rear delts, return under control.',
            ],
            [
                'name' => 'Reverse Pec Deck Fly',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
                'description' => 'Chest on pad, arms on handles/pads, sweep arms back and out, squeeze rear delts, return slowly.',
            ],
            [
                'name' => 'Dumbbell Rear Delt Flyes',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
                'description' => 'Hinge forward with flat back, raise dumbbells out to sides, pause, lower under control without swinging.',
            ],

            [
                'name' => 'Hyperextensions',

                'default_rest_sec' => 60,
                'primary' => ['Lower Back'],
                'secondary' => ['Glutes', 'Hamstrings', 'Core'],
                'description' => 'On back extension bench, hinge at hips to lower torso, then extend to neutral spine (not hyperextended), squeeze glutes.',
            ],
            [
                'name' => '45-Degree Back Extension',

                'default_rest_sec' => 60,
                'primary' => ['Lower Back'],
                'secondary' => ['Glutes', 'Hamstrings', 'Core'],
                'description' => 'On 45° bench, lower torso by hinging at hips, then raise back to neutral while keeping core braced.',
            ],
            [
                'name' => 'Machine Back Extension',

                'default_rest_sec' => 60,
                'primary' => ['Lower Back'],
                'secondary' => ['Glutes', 'Hamstrings', 'Core'],
                'description' => 'Adjust pad to hip crease, hinge forward slightly, extend back to neutral while keeping ribs down and movement controlled.',
            ],

            [
                'name' => 'Inverted Row',

                'default_rest_sec' => 90,
                'primary' => ['Upper Back', 'Lats'],
                'secondary' => ['Biceps', 'Core', 'Forearms', 'Rear Delts'],
                'description' => 'Under a bar/smith, keep body straight, pull chest to bar, squeeze shoulder blades, lower to full extension.',
            ],

            // =========================
            // LEGS
            // =========================

            [
                'name' => 'Back Squat',

                'default_rest_sec' => 150,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core', 'Lower Back'],
                'description' => 'Bar on upper back, brace core, squat down keeping knees tracking over toes, reach depth, drive up through mid-foot.',
            ],
            [
                'name' => 'Front Squat',

                'default_rest_sec' => 150,
                'primary' => ['Quads'],
                'secondary' => ['Glutes', 'Core', 'Upper Back'],
                'description' => 'Bar on front delts with elbows high, stay upright, squat down, drive up keeping chest tall.',
            ],
            [
                'name' => 'Goblet Squat',

                'default_rest_sec' => 90,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Hold dumbbell at chest, sit hips down and back, keep chest up, stand by pushing floor away.',
            ],
            [
                'name' => 'Box Squat',

                'default_rest_sec' => 120,
                'primary' => ['Glutes', 'Quads'],
                'secondary' => ['Hamstrings', 'Core', 'Lower Back'],
                'description' => 'Squat back to a box, lightly sit without collapsing, keep tension, drive up explosively.',
            ],

            [
                'name' => 'Smith Machine Squat',

                'default_rest_sec' => 120,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings'],
                'description' => 'Set feet slightly forward, squat down with control, keep knees tracking, press up without locking knees harshly.',
            ],
            [
                'name' => 'Smith Machine Split Squat',

                'default_rest_sec' => 120,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'In split stance with back foot elevated on smith bar or platform, lower until front thigh is near parallel, press up through front foot, keep torso upright.',
            ],
            [
                'name' => 'Hack Squat Machine',

                'default_rest_sec' => 120,
                'primary' => ['Quads'],
                'secondary' => ['Glutes', 'Hamstrings'],
                'description' => 'Back against pad, feet shoulder width, lower until deep knee bend, push through mid-foot to stand.',
            ],
            [
                'name' => 'Pendulum Squat Machine',

                'default_rest_sec' => 120,
                'primary' => ['Quads'],
                'secondary' => ['Glutes', 'Hamstrings'],
                'description' => 'Set stance, lower through the guided arc to depth, drive up smoothly while keeping hips and back against pad.',
            ],

            [
                'name' => 'Hip Thrust (Barbell)',

                'default_rest_sec' => 120,
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Upper back on bench, bar over hips, drive hips up to full extension, squeeze glutes, lower until hips drop below bench line.',
            ],
            [
                'name' => 'Hip Thrust Machine',

                'default_rest_sec' => 120,
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings'],
                'description' => 'Set pad across hips, drive hips up, pause and squeeze, lower under control without bouncing.',
            ],
            [
                'name' => 'Glute Bridge',

                'default_rest_sec' => 90,
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Lie on floor, feet planted, drive hips up to straight line from knees to shoulders, squeeze glutes, lower slowly.',
            ],

            [
                'name' => 'Walking Lunges',

                'default_rest_sec' => 90,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Step forward, lower until back knee nearly touches floor, push through front foot to stand and step into next rep.',
            ],
            [
                'name' => 'Stationary Lunges',

                'default_rest_sec' => 90,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Stay in split stance, lower straight down, press up through front foot; keep torso stable.',
            ],
            [
                'name' => 'Reverse Lunges',

                'default_rest_sec' => 90,
                'primary' => ['Glutes', 'Quads'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Step back into a lunge, lower with control, drive through front foot to return to standing.',
            ],
            [
                'name' => 'Bulgarian Split Squat',

                'default_rest_sec' => 120,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Back foot elevated, lower until front thigh is near parallel, push through front foot to stand; keep knee tracking.',
            ],
            [
                'name' => 'Split Squat',

                'default_rest_sec' => 90,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'In a split stance, lower hips down, keep torso upright, press through front foot to return up.',
            ],
            [
                'name' => 'Step-Ups',

                'default_rest_sec' => 90,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Step onto a box/bench with one leg, drive through the top foot to stand, control the descent back down.',
            ],

            [
                'name' => 'Leg Press',

                'default_rest_sec' => 120,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings'],
                'description' => 'Feet on platform, lower sled until knees bend deeply without hips lifting, press up without locking knees hard.',
            ],
            [
                'name' => 'Horizontal Leg Press',

                'default_rest_sec' => 120,
                'primary' => ['Quads'],
                'secondary' => ['Glutes', 'Hamstrings'],
                'description' => 'Sit with back supported, lower platform until knees are comfortably bent, press out smoothly to near extension.',
            ],
            [
                'name' => '45-Degree Leg Press',

                'default_rest_sec' => 120,
                'primary' => ['Quads'],
                'secondary' => ['Glutes', 'Hamstrings'],
                'description' => 'Lower sled with control, keep knees tracking, press up through mid-foot while keeping hips planted.',
            ],

            [
                'name' => 'Leg Extensions',

                'default_rest_sec' => 60,
                'primary' => ['Quads'],
                'secondary' => [],
                'description' => 'Set pad above ankles, extend knees to lift weight, squeeze quads, lower slowly to full bend.',
            ],
            [
                'name' => 'Single-Leg Extension',

                'default_rest_sec' => 60,
                'primary' => ['Quads'],
                'secondary' => [],
                'description' => 'Extend one leg at a time on the leg extension, control up and down, avoid swinging.',
            ],

            [
                'name' => 'Lying Leg Curl',

                'default_rest_sec' => 60,
                'primary' => ['Hamstrings'],
                'secondary' => [],
                'description' => 'Lie face down, curl pad toward glutes, squeeze hamstrings, lower slowly to full extension.',
            ],
            [
                'name' => 'Seated Leg Curl',

                'default_rest_sec' => 60,
                'primary' => ['Hamstrings'],
                'secondary' => [],
                'description' => 'Sit with pad above ankles, curl down, pause, return slowly while keeping hips pinned to seat.',
            ],
            [
                'name' => 'Standing Leg Curl',

                'default_rest_sec' => 60,
                'primary' => ['Hamstrings'],
                'secondary' => [],
                'description' => 'Stand braced on machine, curl heel toward glutes, squeeze, lower under control.',
            ],
            [
                'name' => 'Nordic Hamstring Curl',

                'default_rest_sec' => 120,
                'primary' => ['Hamstrings'],
                'secondary' => ['Glutes', 'Core'],
                'description' => 'Anchor ankles, keep body straight, slowly lower torso forward using hamstrings, catch with hands, push lightly to return.',
            ],

            [
                'name' => 'Cable Glute Kickbacks',

                'default_rest_sec' => 60,
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'With ankle strap, kick leg back while keeping torso stable, squeeze glute at top, return slowly.',
            ],
            [
                'name' => 'Machine Glute Kickback',

                'default_rest_sec' => 60,
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings'],
                'description' => 'Brace torso on machine, drive heel back/up, squeeze glute, return under control.',
            ],
            [
                'name' => 'Hip Abduction Machine',

                'default_rest_sec' => 60,
                'primary' => ['Glutes'],
                'secondary' => [],
                'description' => 'Sit with pads on knees, spread legs outward, pause and squeeze, return slowly without bouncing.',
            ],

            [
                'name' => 'Standing Calf Raises',

                'default_rest_sec' => 45,
                'primary' => ['Calves'],
                'secondary' => [],
                'description' => 'Stand on edge/step, rise onto toes, squeeze calves, lower heel below step for stretch, repeat.',
            ],
            [
                'name' => 'Seated Calf Raises',

                'default_rest_sec' => 45,
                'primary' => ['Calves'],
                'secondary' => [],
                'description' => 'Sit with pads on thighs, raise heels up, squeeze, lower to stretch with control.',
            ],
            [
                'name' => 'Leg Press Calf Raises',

                'default_rest_sec' => 45,
                'primary' => ['Calves'],
                'secondary' => [],
                'description' => 'On leg press, place toes on platform edge, extend ankles to press with calves, lower heels to stretch.',
            ],
            [
                'name' => 'Single-Leg Calf Raises',

                'default_rest_sec' => 45,
                'primary' => ['Calves'],
                'secondary' => ['Core'],
                'description' => 'Stand on one leg on a step, raise heel up, squeeze, lower to stretch; use support for balance if needed.',
            ],

            [
                'name' => 'Bodyweight Squats',

                'default_rest_sec' => 60,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Core'],
                'description' => 'Feet shoulder width, squat down to depth, keep knees tracking over toes, stand up by pushing through mid-foot.',
            ],
            [
                'name' => 'Jump Squats',

                'default_rest_sec' => 75,
                'primary' => ['Quads', 'Glutes'],
                'secondary' => ['Hamstrings', 'Calves', 'Core'],
                'description' => 'Squat down, explode upward into a jump, land softly with knees tracking, immediately reset into next rep.',
            ],
            [
                'name' => 'Wall Sit',

                'default_rest_sec' => 60,
                'primary' => ['Quads'],
                'secondary' => ['Glutes'],
                'description' => 'Back against wall, slide down until knees ~90°, keep core braced, hold position without shifting.',
            ],

            // =========================
            // SHOULDERS
            // =========================

            [
                'name' => 'Overhead Barbell Press',

                'default_rest_sec' => 120,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps', 'Core', 'Upper Back'],
                'description' => 'Stand with bar at shoulders, brace core, press overhead until arms lock, lower to shoulders under control.',
            ],
            [
                'name' => 'Seated Barbell Shoulder Press',

                'default_rest_sec' => 120,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps', 'Core'],
                'description' => 'Seated upright, press bar overhead, avoid leaning back excessively, lower to chin/upper chest level with control.',
            ],
            [
                'name' => 'Dumbbell Shoulder Press',

                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps', 'Core'],
                'description' => 'Start dumbbells at shoulder height, press overhead, pause, lower back to shoulders while keeping ribs down.',
            ],
            [
                'name' => 'Arnold Press',

                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Start with palms facing you at shoulders, rotate palms forward as you press overhead, reverse rotation on the way down.',
            ],

            [
                'name' => 'Machine Shoulder Press',

                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Adjust seat so handles start at shoulder level, press up, pause, return slowly without shrugging.',
            ],
            [
                'name' => 'Smith Machine Shoulder Press',

                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps'],
                'description' => 'Set bench upright, unrack smith bar at shoulder level, press up, lower back down keeping elbows under bar.',
            ],

            [
                'name' => 'Dumbbell Lateral Raises',

                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => [],
                'description' => 'With slight elbow bend, raise dumbbells out to shoulder height, pause, lower slowly without swinging.',
            ],
            [
                'name' => 'Seated Dumbbell Lateral Raises',

                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => [],
                'description' => 'Seated upright, lift dumbbells out to sides, keep shoulders down, lower under control.',
            ],
            [
                'name' => 'Cable Lateral Raises',

                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => [],
                'description' => 'With cable at low setting, raise handle out to side to shoulder height, pause, return slowly keeping tension.',
            ],
            [
                'name' => 'Machine Lateral Raise',

                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => [],
                'description' => 'Adjust seat/pads, lift arms out to sides, squeeze side delts, return smoothly.',
            ],
            [
                'name' => 'Single-Arm Cable Lateral Raise',

                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => ['Core'],
                'description' => 'Stand tall, raise one cable handle to the side to shoulder height, resist leaning, lower slowly; switch sides.',
            ],

            [
                'name' => 'Dumbbell Front Raises',

                'default_rest_sec' => 60,
                'primary' => ['Front Delts'],
                'secondary' => [],
                'description' => 'Raise dumbbells straight in front to shoulder height, pause, lower under control while keeping torso still.',
            ],
            [
                'name' => 'Plate Front Raises',

                'default_rest_sec' => 60,
                'primary' => ['Front Delts'],
                'secondary' => [],
                'description' => 'Hold plate with both hands, raise to shoulder height, pause, lower slowly without leaning back.',
            ],
            [
                'name' => 'Cable Front Raises',

                'default_rest_sec' => 60,
                'primary' => ['Front Delts'],
                'secondary' => [],
                'description' => 'With cable low, raise handle forward to shoulder height, pause, return slowly keeping tension.',
            ],
            [
                'name' => 'Barbell Front Raises',

                'default_rest_sec' => 60,
                'primary' => ['Front Delts'],
                'secondary' => [],
                'description' => 'Hold barbell at thighs, raise to shoulder height with straight arms (soft elbows), lower under control.',
            ],

            [
                'name' => 'Reverse Pec Deck Fly',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
                'description' => 'Chest against pad, sweep arms back/out, squeeze rear delts, return slowly without shrugging.',
            ],
            [
                'name' => 'Reverse Cable Flyes',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
                'description' => 'Cross cables, pull arms out and back to shoulder line, pause, return under control.',
            ],
            [
                'name' => 'Bent-Over Cable Rear Delt Fly',

                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
                'description' => 'Hinge forward, pull cables out to sides, squeeze rear delts, return slowly with steady torso.',
            ],

            [
                'name' => 'Upright Row (Barbell)',

                'default_rest_sec' => 90,
                'primary' => ['Side Delts'],
                'secondary' => ['Trapezius', 'Biceps'],
                'description' => 'Hold bar in front, pull elbows up and out to chest height, pause, lower under control; avoid excessive range if uncomfortable.',
            ],
            [
                'name' => 'Upright Row (Cable)',

                'default_rest_sec' => 90,
                'primary' => ['Side Delts'],
                'secondary' => ['Trapezius', 'Biceps'],
                'description' => 'Using low cable, pull handle up toward upper chest with elbows high, pause, return slowly.',
            ],
            [
                'name' => 'High Pull',

                'default_rest_sec' => 120,
                'primary' => ['Side Delts', 'Front Delts'],
                'secondary' => ['Trapezius', 'Upper Back', 'Core'],
                'description' => 'From hang position, explosively extend hips then pull bar upward with elbows high, control the return to start.',
            ],

            [
                'name' => 'Pike Push-ups',

                'default_rest_sec' => 75,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps', 'Core'],
                'description' => 'Hips high in pike, lower head toward floor between hands, press back up while keeping core braced.',
            ],
            [
                'name' => 'Handstand Push-ups',

                'default_rest_sec' => 120,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps', 'Core'],
                'description' => 'In a handstand (often against a wall), lower head under control, press back up to full elbow extension.',
            ],
            [
                'name' => 'Wall-Supported Handstand Hold',

                'default_rest_sec' => 60,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Core'],
                'description' => 'Kick into handstand against wall, keep arms locked and shoulders elevated, brace core and hold stable position.',
            ],

            // =========================
            // ARMS
            // =========================

            [
                'name' => 'Chin-ups (Biceps Focus)',

                'default_rest_sec' => 120,
                'primary' => ['Biceps'],
                'secondary' => ['Lats', 'Upper Back', 'Forearms', 'Core'],
                'description' => 'Use underhand grip, pull until chin clears bar, squeeze biceps/lats, lower to full extension with control.',
            ],
            [
                'name' => 'Close-Grip Lat Pulldown (Biceps Focus)',

                'default_rest_sec' => 90,
                'primary' => ['Biceps'],
                'secondary' => ['Lats', 'Upper Back', 'Forearms'],
                'description' => 'Use close neutral handle, pull to upper chest while keeping elbows tucked, pause, return slowly to full stretch.',
            ],

            [
                'name' => 'Barbell Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Stand tall, curl bar up without swinging, keep elbows near sides, squeeze at top, lower slowly.',
            ],
            [
                'name' => 'EZ-Bar Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Grip EZ-bar comfortably, curl to shoulder height, pause, lower with control keeping elbows fixed.',
            ],
            [
                'name' => 'Dumbbell Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Curl dumbbells up with palms rotating to face you, squeeze, lower slowly keeping shoulders relaxed.',
            ],
            [
                'name' => 'Alternating Dumbbell Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Curl one dumbbell at a time with control, keep torso still, alternate sides each rep.',
            ],
            [
                'name' => 'Hammer Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps', 'Forearms'],
                'secondary' => [],
                'description' => 'Hold dumbbells with neutral grip (thumbs up), curl up, squeeze, lower slowly without swinging.',
            ],
            [
                'name' => 'Incline Dumbbell Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Seated on incline bench with arms hanging, curl up without moving shoulders forward, lower to full stretch.',
            ],
            [
                'name' => 'Concentration Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => [],
                'description' => 'Seated, brace elbow against inner thigh, curl dumbbell to shoulder, squeeze, lower slowly.',
            ],
            [
                'name' => 'Preacher Curl (EZ-Bar)',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Upper arms on preacher pad, curl EZ-bar up, squeeze biceps, lower slowly to near full extension.',
            ],
            [
                'name' => 'Preacher Curl (Machine)',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => [],
                'description' => 'Set seat/pad, curl handles up, pause, lower under control while keeping upper arms pinned.',
            ],

            [
                'name' => 'Cable Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Use straight bar on low cable, curl up keeping elbows near sides, squeeze, return slowly.',
            ],
            [
                'name' => 'Rope Hammer Curl (Cable)',

                'default_rest_sec' => 60,
                'primary' => ['Biceps', 'Forearms'],
                'secondary' => [],
                'description' => 'With rope on low cable, curl with neutral grip, keep elbows tight, lower slowly maintaining tension.',
            ],
            [
                'name' => 'Single-Arm Cable Curl',

                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'description' => 'Use single handle on low cable, curl to shoulder height, squeeze, return slowly; repeat other side.',
            ],

            [
                'name' => 'Close-Grip Bench Press (Triceps Focus)',

                'default_rest_sec' => 120,
                'primary' => ['Triceps'],
                'secondary' => ['Chest', 'Front Delts'],
                'description' => 'Grip bar just inside shoulder width, lower to lower chest, press up while keeping elbows closer to torso.',
            ],
            [
                'name' => 'Dips (Triceps)',

                'default_rest_sec' => 120,
                'primary' => ['Triceps'],
                'secondary' => ['Chest', 'Front Delts'],
                'description' => 'Stay more upright than chest dips, lower until elbows bend comfortably, press up to lockout keeping elbows tracking back.',
            ],
            [
                'name' => 'Assisted Dips (Triceps)',

                'default_rest_sec' => 90,
                'primary' => ['Triceps'],
                'secondary' => ['Chest', 'Front Delts'],
                'description' => 'Use assisted machine, keep torso upright, lower under control, press up focusing on triceps extension.',
            ],

            [
                'name' => 'Triceps Pushdown (Cable)',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Elbows pinned to sides, press bar down until arms extend, squeeze triceps, return to ~90° elbow bend.',
            ],
            [
                'name' => 'Rope Triceps Pushdown',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Press rope down and slightly apart at bottom, squeeze, return with control keeping elbows tucked.',
            ],
            [
                'name' => 'Reverse-Grip Triceps Pushdown',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Use underhand grip on bar, elbows pinned to sides, press bar down until arms extend, squeeze triceps, return to ~90° elbow bend.',
            ],
            [
                'name' => 'Single-Arm Triceps Pushdown',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => ['Core'],
                'description' => 'One handle, elbow pinned, extend arm down, squeeze, return slowly; brace core to avoid twisting.',
            ],

            [
                'name' => 'Overhead Triceps Extension (Dumbbell)',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => ['Core'],
                'description' => 'Hold one dumbbell overhead, lower behind head by bending elbows, extend back up without flaring ribs.',
            ],
            [
                'name' => 'Seated Overhead Triceps Extension (Dumbbell)',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => ['Core'],
                'description' => 'Seated tall, lower dumbbell behind head, extend elbows to raise it back overhead, keep elbows pointing forward.',
            ],
            [
                'name' => 'Cable Overhead Triceps Extension',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => ['Core'],
                'description' => 'Face away from cable, elbows near head, extend arms overhead, squeeze, return to a deep stretch.',
            ],
            [
                'name' => 'Single-Arm Overhead Cable Triceps Extension',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => ['Core'],
                'description' => 'With one handle, extend overhead, keep elbow fixed, return slowly; keep torso stable.',
            ],

            [
                'name' => 'Skull Crushers (EZ-Bar)',

                'default_rest_sec' => 75,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Lie on bench, lower EZ-bar toward forehead by bending elbows, then extend elbows to raise back up.',
            ],
            [
                'name' => 'Dumbbell Skull Crushers',

                'default_rest_sec' => 75,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Lie on bench, lower dumbbells beside head, extend elbows to press back up, keep upper arms steady.',
            ],
            [
                'name' => 'Triceps Kickbacks (Dumbbell)',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Hinge forward, upper arm parallel to floor, extend elbow to straighten arm back, squeeze, return slowly.',
            ],

            [
                'name' => 'Triceps Extension Machine',

                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
                'description' => 'Adjust seat so elbows align with pivot, extend handles until arms straighten, squeeze, return under control.',
            ],

            [
                'name' => 'Wrist Curls',

                'default_rest_sec' => 45,
                'primary' => ['Forearms'],
                'secondary' => [],
                'description' => 'Forearms supported on bench/thighs, curl wrists up to flex, lower to stretch slowly.',
            ],
            [
                'name' => 'Reverse Wrist Curls',

                'default_rest_sec' => 45,
                'primary' => ['Forearms'],
                'secondary' => [],
                'description' => 'Forearms supported, palms down, extend wrists up, pause, lower to stretch with control.',
            ],
            [
                'name' => 'Farmer\'s Walk',

                'default_rest_sec' => 90,
                'primary' => ['Forearms'],
                'secondary' => ['Trapezius', 'Core'],
                'description' => 'Hold heavy dumbbells/kettlebells at sides, walk with tall posture and braced core, avoid shrugging, set down safely.',
            ],

            // =========================
            // CORE / ABS
            // =========================

            [
                'name' => 'Ab Crunch Machine',

                'default_rest_sec' => 45,
                'primary' => ['Abs'],
                'secondary' => [],
                'description' => 'Sit in machine with pads on chest/shoulders, crunch forward by flexing abs, squeeze at top, return slowly to starting position.',
            ],
            [
                'name' => 'Cable Woodchoppers',

                'default_rest_sec' => 60,
                'primary' => ['Abs', 'Obliques'],
                'secondary' => ['Core'],
                'description' => 'Set cable high, stand sideways, pull cable down and across body in a chopping motion, rotate torso, return slowly; repeat other side.',
            ],
            [
                'name' => 'Knee Raises',

                'default_rest_sec' => 60,
                'primary' => ['Abs'],
                'secondary' => ['Core'],
                'description' => 'Hang from bar or use captain\'s chair, raise knees toward chest, squeeze abs at top, lower slowly with control.',
            ],
            [
                'name' => 'Straight Leg Raises',

                'default_rest_sec' => 60,
                'primary' => ['Abs'],
                'secondary' => ['Core'],
                'description' => 'Hang from bar or lie on back, raise straight legs up toward ceiling, keep core braced, lower slowly with control.',
            ],

        ];

        // Check if muscle image service is configured
        $canFetchImages = $this->muscleImageService->isConfigured();

        if (! $canFetchImages) {
            $this->command->warn('RAPIDAPI_KEY not configured - muscle group images will not be fetched.');
        }

        $imagesFetched = 0;
        $imagesSkipped = 0;

        foreach ($exercises as $exerciseData) {
            $muscleGroupImagePath = null;

            // Fetch muscle group image if service is configured
            if ($canFetchImages) {
                $primaryMuscles = $exerciseData['primary'];
                $secondaryMuscles = $exerciseData['secondary'];

                // Check if we already have this image (to avoid duplicate API calls)
                if ($this->muscleImageService->imageExists($primaryMuscles, $secondaryMuscles)) {
                    $muscleGroupImagePath = $this->muscleImageService->getImagePath($primaryMuscles, $secondaryMuscles);
                    $imagesSkipped++;
                } else {
                    $muscleGroupImagePath = $this->muscleImageService->fetchAndStoreMuscleImage(
                        $primaryMuscles,
                        $secondaryMuscles
                    );

                    if ($muscleGroupImagePath !== null) {
                        $imagesFetched++;
                        $this->command->info("Fetched image for: {$exerciseData['name']}");
                    }
                }
            }

            $exercise = Exercise::firstOrCreate(
                [
                    'name' => $exerciseData['name'],
                ],
                [
                    'category_id' => $categories[$exerciseData['category']] ?? null,
                    'default_rest_sec' => $exerciseData['default_rest_sec'],
                    'muscle_group_image' => $muscleGroupImagePath,
                ]
            );

            // Update muscle_group_image if exercise exists but has no image
            if (! $exercise->wasRecentlyCreated && empty($exercise->muscle_group_image) && $muscleGroupImagePath !== null) {
                $exercise->update(['muscle_group_image' => $muscleGroupImagePath]);
            }

            // Attach muscle groups if the exercise was newly created or has no muscle groups
            if ($exercise->wasRecentlyCreated || $exercise->muscleGroups()->count() === 0) {
                $muscleGroupAttachments = [];

                // Add primary muscle groups
                foreach ($exerciseData['primary'] as $muscleName) {
                    if (isset($muscleGroups[$muscleName])) {
                        $muscleGroupAttachments[$muscleGroups[$muscleName]] = ['is_primary' => true];
                    }
                }

                // Add secondary muscle groups
                foreach ($exerciseData['secondary'] as $muscleName) {
                    if (isset($muscleGroups[$muscleName])) {
                        $muscleGroupAttachments[$muscleGroups[$muscleName]] = ['is_primary' => false];
                    }
                }

                $exercise->muscleGroups()->sync($muscleGroupAttachments);
            }
        }

        $this->command->info('Global exercises seeded successfully with muscle groups!');

        if ($canFetchImages) {
            $this->command->info("Muscle images: {$imagesFetched} fetched, {$imagesSkipped} already existed.");
        }
    }
}
