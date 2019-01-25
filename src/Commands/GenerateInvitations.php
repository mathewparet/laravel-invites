<?php

namespace mathewparet\LaravelInvites\Commands;

use mathewparet\LaravelInvites\Facades\LaravelInvites;
use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

use Illuminate\Console\Command;

class GenerateInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:generate {email?} 
        {--a|allow=1 : Number of times the code can be used} 
        {--c|count=1 : The number of codes to be generated}
        {--d|days= : The number of days until expiry (preceeds hours option)}
        {--r|hours= : Number of hours until expiry}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates invitation codes';

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
        $allow = $this->option('allow');
        $count = $this->option('count');
        $hours = (int) $this->option('hours');
        $days = (int) $this->option('days');

        try
        {
            $invite = LaravelInvites::for ($email) {
                ->allow($allow);
            }

            if ($days) {
                            $invite->setExpiry(now()->addDays($days));
            } else if ($hours) {
                            $invite->setExpiry(now()->addHours($hours));
            }
    
            $invite->generate($count);
    
            $this->info($count." invitations generated.");                
        } catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
