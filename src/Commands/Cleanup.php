<?php

namespace mathewparet\LaravelInvites\Commands;

use mathewparet\LaravelInvites\Models\Invite;

use Illuminate\Console\Command;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired and maximum utilized invitations from DB';

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
        $count = Invite::useless()->count();
        Invite::useless()->delete();
        $this->info('Removed './** @scrutinizer ignore-type */ $count.' unusabled invitation codes');
    }
}
