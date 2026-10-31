use App\Http\Controllers\Api\V1\PropertyController as PropertyV1Controller;
use App\Http\Controllers\Api\V2\PropertyController as PropertyV2Controller;

// مسارات النسخة الأولى V1
Route::prefix('v1')->group(function () {
    Route::apiResource('properties', PropertyV1Controller::class);
});

// مسارات النسخة الثانية V2
Route::prefix('v2')->group(function () {
    Route::apiResource('properties', PropertyV2Controller::class);
});