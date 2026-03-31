<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Ingredient extends Model
{
    public function pizzas(): BelongsToMany
    {
        return $this->belongsToMany(Pizza::class);
    }
}
