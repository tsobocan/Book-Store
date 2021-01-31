<?php

namespace App\Console\Commands;


use App\Models\Models\Status;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SetupDataCommand extends Command
{

    protected $signature = 'setup:data';

    protected $description = 'Data saving...';

    public function handle()
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);

        Status::create(['title' => 'Reserved']);
        Status::create(['title' => 'Rented']);
        Status::create(['title' => 'Completed']);
    }
}
