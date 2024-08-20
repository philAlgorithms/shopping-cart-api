<?php

namespace App\Providers;

use App\Handler\Session\PolymorphicDatabaseSessionHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        $this->generateTemporaryUrls();

        /**
         * Combines pluck and to array methods of Eloquent Collection
         */
        Collection::macro('pluckToArray', function ($column) {
            return $this->pluck($column)->toArray();
        });

        // Add custom session handlers
        Session::extend('polymorphic-database', function ($app) {
            $table = $app['config']['session.table'];
            $lifetime = $app['config']['session.lifetime'];
            $connection = $app['db']->connection($app['config']['session.connection']);

            return new PolymorphicDatabaseSessionHandler($connection, $table, $lifetime, $app);
        });
    }

    private function generateTemporaryUrls()
    {
        $disks = config('filesystems.disks');
        foreach (Arr::except($disks, ['s3', 'google']) as $name => $attributes) {
            Storage::disk($name)->buildTemporaryUrlsUsing(function ($path, $expiration, $options) use ($name) {
                $encrypted_path = Crypt::encryptString($path);
                return URL::temporarySignedRoute(
                    'file.temp',
                    $expiration,
                    array_merge(
                        $options,
                        [
                            'path' => $encrypted_path,
                            'disk' => $name,
                        ]
                    ),
                    false
                );
            });
        }
    }
}
