<?php
namespace App\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Libs\Validation;

class RecipesController
{
    /**
     * @param  Request http request object
     * @param  Recipe Recipe model
     * @return mixed $resp|error
     */
    public function index(Request $request, Recipe $recipe)
    {
        $recipe = $recipe->newQuery();

        if ($request->has('vegetarian')) {
            $recipe->where('vegetarian', (int)$request->vegetarian);
        }

        if ($request->has('difficulty')) {
            $recipe->where('difficulty', (int)$request->difficulty);
        }

        if ($request->has('prepTime')) {
            $prepTimeLike = '%'.$request->prepTime.'%';
            $recipe->where('prepTime', 'like', $prepTimeLike);
        }

        if ($request->has('name')) {
            $nameLike = '%'.$request->name.'%';
            $recipe->where('name', 'like', $nameLike);
        }

        $offset = 0;
        $limit = 10;
        $page = 1;

        if ($request->has('page')) {
            $page = (int)$request->page;
            if ($page == 0) $page = 1;
            $offset = ($page * $limit) - $limit;
        }

        $data = $recipe->with('avgRating')->offset($offset)->limit($limit)->get();

        if (count($data)) {
            $resp = [];
            $resp['totalRecords'] = $recipe->count();
            $resp['page'] = $page;
            $resp['limit'] = $limit;
            $resp['data'] = $data;
            return $resp;
        }

        return response(['error' => 404], 404);
    }

    /**
     * @param  Request http request object
     * @return mixed $recipe|error
     */
    public function create(Request $request)
    {        
        $errors = $this->validateRequest($request);
        if ($errors) return response($errors, 400);

        $recipe = new Recipe;
        $recipe->name = $request->name;
        $recipe->prepTime = $request->prepTime;
        $recipe->difficulty = $request->difficulty;
        $recipe->vegetarian = $request->vegetarian;
        $recipe->userId = authUserId();
        $recipe->save();

        return $recipe;
    }

    /**
     * @param  int $id
     * @return mixed $recipe|error
     */
    public function show($id)
    {
        $recipe = Recipe::with('avgRating')->find($id);
        if ($recipe) return $recipe;

        return response(['error' => 'Invalid recipe id'], 404);
    }

    /**
     * @param  int $id
     * @param  Request http request object
     * @return mixed $recipe|error
     */
    public function update($id, Request $request)
    {
        $errors = $this->validateRequest($request);
        if ($errors) return response($errors, 400);

        $recipe = Recipe::where('id', $id)
            ->where('userId', authUserId())
            ->first();

        if ($recipe) {
            $recipe->name = $request->name;
            $recipe->prepTime = $request->prepTime;
            $recipe->difficulty = $request->difficulty;
            $recipe->vegetarian = $request->vegetarian;
            $recipe->save();

            return $recipe;
        }

        return response(['error' => 'Invalid recipe id'], 404);
    }

    /**
     * @param  int $id
     * @return object error|deleted
     */
    public function destory($id)
    {
        $recipe = Recipe::where('id', $id)
            ->where('userId', authUserId())
            ->first();

        if ($recipe) {
            $recipe->delete();
            return ['deleted' => true];
        }

        return response(['error' => 'Invalid recipe id'], 404);
    }

    /**
     * @param  Request http request object
     * @return array $errors
     */
    private function validateRequest($request)
    {
        $validation = Validation::getInstance();

        $data = [
            'name' => $request->name,
            'prepTime' => $request->prepTime,
            'difficulty' => $request->difficulty,
            'vegetarian' => $request->vegetarian,
        ];

        $rules = [
            'name' => 'required',
            'prepTime' => 'required',
            'difficulty' => 'required|integer|between:1,3',
            'vegetarian' => 'required|boolean',
        ];

        $errors = null;

        $validator = $validation->make($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
        }

        return $errors;
    }
}
