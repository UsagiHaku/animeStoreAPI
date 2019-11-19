<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageSerieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_serie', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('package_id')->unsigned();
            $table->foreign('package_id')
                ->references('id')
                ->on('packages');
            $table->integer('serie_id')->unsigned();
            $table->foreign('serie_id')
                ->references('id')
                ->on('series');
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
        Schema::dropIfExists('package_serie');
    }
}
