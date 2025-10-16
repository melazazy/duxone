<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

// class CreateTenant extends Command
// {
//     protected $signature = 'tenant:create {id} {domain}';
//     protected $description = 'Create a tenant with a domain';

//     public function handle()
//     {
//         $id = $this->argument('id');
//         $domain = $this->argument('domain');

//         $tenant = Tenant::create(['id' => $id]);
//         Domain::create([
//             'domain' => $domain,
//             'tenant_id' => $tenant->id,
//         ]);

//         $this->info("Tenant [$id] with domain [$domain] created successfully.");
//     }
// }
