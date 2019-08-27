<?php
use Illuminate\Database\Schema\Blueprint;
use App\Libs\Migration;

class CreateRecipesTable extends Migration
{
    public function up()
    {
        $this->schema->create('recipes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('prepTime');
            $table->integer('difficulty');
            $table->boolean('vegetarian');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('recipes');
    }
}
