use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Property;
use App\Models\Office;

public function boot(): void
{
    Relation::morphMap([
        'property' => Property::class,
        'office'   => Office::class,
    ]);
}