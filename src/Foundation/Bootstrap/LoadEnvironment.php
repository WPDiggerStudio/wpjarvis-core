<?php
declare(strict_types=1);

namespace WPJarvis\Core\Foundation\Bootstrap;

use Dotenv\Dotenv;
use WPJarvis\Core\Foundation\Application;

/**
 * Class LoadEnvironment
 *
 * Loads the `.env` file from the base path using vlucas/phpdotenv.
 * Provides Laravel-style environment configuration support.
 *
 * @package WPJarvis\Core\Foundation\Bootstrap
 */
class LoadEnvironment
{
    /**
     * Bootstrap the loading of environment variables from `.env`.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $envPath = $app->basePath();

        if (file_exists($envPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($envPath);
            $dotenv->safeLoad(); // Gracefully handle missing vars
        }
    }
}
