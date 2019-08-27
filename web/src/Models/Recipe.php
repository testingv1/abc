<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Recipe extends Eloquent
{
    public static $snakeAttributes = false;

    public function avgRating()
    {
        return $this->hasOne('App\Models\Rating', 'recipeId')
            ->selectRaw('"recipeId", round(avg(rating), 1) as rating')
            ->groupBy('recipeId');
    }

    public function getAvgRatingAttribute()
    {
        if (!array_key_exists('avgRating', $this->relations)) {
            $this->load('avgRating');
        }

        $related = $this->getRelation('avgRating');
        return ($related) ? (int) $related->aggregate : 0;
    }   
}
