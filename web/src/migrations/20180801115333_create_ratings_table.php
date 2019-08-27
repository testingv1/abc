<?php
use Illuminate\Database\Schema\Blueprint;
use App\Libs\Migration;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        $this->schema->create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recipeId');
            $table->integer('rating');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('ratings');
    }
}
