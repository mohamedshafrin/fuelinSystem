<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_requests', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->integer('district_id');
            $table->integer('station_id');
            $table->integer('customer_id');
            $table->integer('vehicle_id');
            $table->integer('fuel_type');
            $table->date('request_date');
            $table->date('schedule_date')->nullable();
            $table->integer('schedule_id')->nullable();
            $table->decimal('fuel_value',20,2)->default(0);
            $table->decimal('fuel_amount')->default(0);
            $table->integer('status')->default(0)->comment('0 = Not Accept | 1 = Accept');
            $table->integer('pay_status')->default(0)->comment('0 = Not Paid | 1 = Paid');
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
        Schema::dropIfExists('fuel_requests');
    }
}
