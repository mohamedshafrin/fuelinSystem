<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidToFuelStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fuel_stations', function (Blueprint $table) {
            $table->integer('petrol')->default(0)->comment('Type Id = 1')->after('contact');
            $table->integer('av_petrol')->default(0)->after('petrol');
            $table->integer('diesel')->default(0)->comment('Type Id = 2')->after('av_petrol');
            $table->integer('av_diesel')->default(0)->after('diesel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fuel_stations', function (Blueprint $table) {
            //
        });
    }
}
