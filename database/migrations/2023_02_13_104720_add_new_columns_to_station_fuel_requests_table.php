<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToStationFuelRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_fuel_requests', function (Blueprint $table) {
            $table->decimal('alloc_petrol',20,2)->after('pumbed_date')->default(0);
            $table->decimal('alloc_diesel',20,2)->after('petrol')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_fuel_requests', function (Blueprint $table) {
            //
        });
    }
}
