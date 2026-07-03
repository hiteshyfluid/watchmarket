<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BackfillUserUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-user-usernames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign an auto-generated unique username to any user that does not have one yet';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::whereNull('username')->orWhere('username', '')->get();

        foreach ($users as $user) {
            $user->username = User::generateUniqueUsername();
            $user->save();
            $this->line("#{$user->id} {$user->name} -> {$user->username}");
        }

        $this->info("Backfilled {$users->count()} user(s).");

        return self::SUCCESS;
    }
}
