<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SetupData extends Command
{
    protected $signature = 'data:setup';

    protected $description = 'Run only once. Saves required data.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'Uporabnik']);
    }
}
