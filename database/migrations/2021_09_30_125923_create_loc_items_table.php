<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loc_items', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->timestamps();
            $table->string('localize_id');
            $table->string('origin')->nullable();
            $table->string('trans')->nullable();
            $table->string('new_trans')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loc_items');
    }
}
