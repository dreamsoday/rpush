<?php
namespace Lgy\RPush;

use Illuminate\Support\ServiceProvider;

class RPushServiceProvider extends ServiceProvider {
    protected $defer = true;

    public function boot() {

        $this->publishes([
            __DIR__.'/../config/rpush.php' => config_path('rpush.php'),
        ]);
    }

    public function register() {
        $this->mergeConfigFrom( __DIR__.'/../config/rpush.php', 'rpush');

        $this->app->singleton('rpush', function($app) {
            $config = $app->make('config');

            $app_key = $config->get('rpush.app_key');
            $master_secret = $config->get('rpush.master_secret');
            $log_file = $config->get('rpush.log_file');

            return new RPushService($app_key, $master_secret, $log_file);
        });
    }

    public function provides() {
        return ['rpush'];
    }
}
