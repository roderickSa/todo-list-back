<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateFirstDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-first-data-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("starting command...");

        $users_to_create = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin12345'),
                'is_admin' => 1,
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('12345'),
            ]
        ];

        $users = \App\Models\User::whereIn('email', array_column($users_to_create, 'email'))->get();

        if (count($users) == 0) {
            \App\Models\User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin12345'),
                'is_admin' => 1,
            ]);

            \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('12345'),
            ]);

            \App\Models\Task::factory(4)->create();
        }

        $this->info("command finished");
    }
}
