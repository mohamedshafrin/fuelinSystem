<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationFuelRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_fuel_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('district_id');
            $table->integer('station_id');
            $table->date('request_date');
            $table->date('scheduled_date')->nullable();
            $table->date('pumbed_date')->nullable();
            $table->decimal('petrol')->default(0);
            $table->decimal('diesel')->default(0);
            $table->integer('status')->default(0)->comment('0 = Not Accept | 1 = Accept | 2 = Dispatch | 3 = Pumbed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('station_fuel_requests');
    }
}
