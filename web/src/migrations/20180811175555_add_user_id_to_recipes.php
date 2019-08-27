<?php
use Illuminate\Database\Schema\Blueprint;
use App\Libs\Migration;

class AddUserIdToRecipes extends Migration
{
    public function up()
    {
        $this->schema->table('recipes', function (Blueprint $table) {
            $table->integer('userId');
        });
    }

    public function down()
    {
        $this->schema->table('recipes', function (Blueprint $table) {
            $table->dropColumn('userId');
        });
    }
}
