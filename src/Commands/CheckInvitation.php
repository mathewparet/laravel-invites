<?php

namespace mathewparet\LaravelInvites\Commands;

use mathewparet\LaravelInvites\Facades\LaravelInvites;

use Illuminate\Console\Command;

use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

class CheckInvitation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:check {code : The invitation code} {email? : Email ID to check against}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the validity of the given code';

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
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email') ?: null;
        $code = $this->argument('code');

        try {
            LaravelInvites::check($code, $email);
            $this->info('This code is valid');
        } catch (LaravelInvitesException $e)
        {
            $this->error($e->getMessage());
        }
    }
}
