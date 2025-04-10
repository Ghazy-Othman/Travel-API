<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Register Travel Observer
// #[ObservedBy([TravelObserve::class])]
class Travel extends Model
{
    //
    use HasFactory , HasUuids;

    protected $table = 'travels';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'number_of_days',
        'is_public',
        'number_of_nights',
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['number_of_days'] - 1
        );
    }
}
