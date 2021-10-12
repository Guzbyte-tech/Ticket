<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("title", 191);
            $table->unsignedBigInteger("user_id");
            $table->string("name", 191);
            $table->string("email", 191);
            $table->unsignedBigInteger("category_id");
            $table->boolean("status_id")->default(false);
            $table->integer("priority_id")->nullable();
            $table->string("attachment", 191)->nullable();
            $table->integer("agent_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
