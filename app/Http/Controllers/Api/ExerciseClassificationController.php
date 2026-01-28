<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AngleResource;
use App\Http\Resources\Api\EquipmentTypeResource;
use App\Http\Resources\Api\MovementPatternResource;
use App\Http\Resources\Api\TargetRegionResource;
use App\Models\Angle;
use App\Models\EquipmentType;
use App\Models\MovementPattern;
use App\Models\TargetRegion;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExerciseClassificationController extends Controller
{
    /**
     * List all movement patterns
     */
    public function movementPatterns(): AnonymousResourceCollection
    {
        $patterns = MovementPattern::orderBy('display_order')->get();

        return MovementPatternResource::collection($patterns);
    }

    /**
     * List all target regions
     */
    public function targetRegions(): AnonymousResourceCollection
    {
        $regions = TargetRegion::orderBy('display_order')->get();

        return TargetRegionResource::collection($regions);
    }

    /**
     * List all equipment types
     */
    public function equipmentTypes(): AnonymousResourceCollection
    {
        $types = EquipmentType::orderBy('display_order')->get();

        return EquipmentTypeResource::collection($types);
    }

    /**
     * List all angles
     */
    public function angles(): AnonymousResourceCollection
    {
        $angles = Angle::orderBy('display_order')->get();

        return AngleResource::collection($angles);
    }
}
