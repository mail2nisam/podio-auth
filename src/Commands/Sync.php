<?php

namespace PodioAuth\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PodioAuth\Models\PodioApi;
use PodioAuth\Models\PodioAppAuth;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all configurations to DB';

    /**
     * Create a new command instance.
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
        /**
         * Sync app configs to database.
         */
        $this->comment("Syncing APIs");
        $this->output->progressStart(count(Config::get('podio.app_auth')));
        foreach (Config::get('podio.app_auth') as $name => $app) {
            $appAuth = PodioAppAuth::firstOrCreate(['app_id' => $app['app_id']]);
            $appAuth->app_name = $name;
            $appAuth->app_secret = $app['app_secret'];
            $appAuth->save();
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();


        /**
         * Sync client APIs to database.
         */
        $this->output->progressStart(count(Config::get('podio.client_api')));
        foreach (Config::get('podio.client_api') as $client) {
            $api = PodioApi::firstOrCreate(['client_id' => $client['id']]);
            $api->client_secret = $client['secret'];
            $api->save();
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->info("Syncing finished");
    }
}
