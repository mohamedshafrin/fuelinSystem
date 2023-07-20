<?php

namespace App\Console\Commands;

use App\Models\CustomerVehicle;
use Illuminate\Console\Command;

class AutoFuelQuotaUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:quota_refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customer_vehicle = CustomerVehicle::all();
        foreach($customer_vehicle as $item)
        {
            $vehicle = CustomerVehicle::find($item->id);
            $vehicle->av_quota = $vehicle->quota;
            $vehicle->update();
        }
    }
}
