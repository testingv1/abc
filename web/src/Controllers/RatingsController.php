<?php
namespace App\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Libs\Validation;

class RatingsController
{
    /**
     * @param  int recipeId
     * @param  Request http request object
     * @return object $rating
     */
    public function create($recipeId, Request $request)
    {
        $errors = $this->validateRequest($request);
        if ($errors) return response($errors, 400);

        $rating = new Rating;
        $rating->recipeId = (int)$recipeId;
        $rating->rating = $request->rating;
        $rating->save();

        return $rating;
    }

    /**
     * @param  Request http request object
     * @return array $errors
     */
    private function validateRequest($request)
    {
        $validation = Validation::getInstance();

        $data = [
            'rating' => $request->rating,
            'recipe' => $request->segment(2)
        ];

        $rules = [
            'rating' => 'required|integer|between:1,5',
            'recipe' => 'required|exists:recipes,id',
        ];

        $errors = null;

        $validator = $validation->make($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
        }

        return $errors;
    }
}
