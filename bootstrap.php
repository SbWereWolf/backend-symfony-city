<?php

require __DIR__ . '/vendor/autoload.php';

use App\Console\FileGeneratorCommand;
use App\Console\FileParserCommand;
use App\Entity\City;
use App\Entity\User;
use App\Interfaces\DatabaseProviderInterface;
use App\Interfaces\FileProviderInterface;
use App\Interfaces\ProviderInterface;
use App\Interfaces\RepositoryInterface;
use App\Provider\DatabaseProvider;
use App\Provider\FileProvider;
use App\Repository\CityDatabaseRepository;
use App\Repository\CityFileRepository;
use App\Repository\UserDatabaseRepository;
use App\Repository\UserFileRepository;
use App\Services\Cleaner;
use App\Services\Parser;
use App\Services\Reporter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Dotenv\Dotenv;

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (is_array($env = @include __DIR__ . '/.env.local.php')
    && ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $env['APP_ENV'])
    === $env['APP_ENV']) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k])
            && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException(
        'Please run "composer require symfony/dotenv"'
        . ' to load the ".env" files configuring the application.');
} else {
    // load all the .env files
    (new Dotenv(false))->loadEnv(__DIR__ . '/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] =
    ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';

DEFINE('VAR_DIR', __DIR__ . '/var/');

$container = new ContainerBuilder();
$container->autowire(PDO::class, PDO::class)
    ->addArgument(sprintf('mysql:host=%s;dbname=%s',
        $_SERVER['APP_MYSQL_HOST'], $_SERVER['APP_MYSQL_DATABASE']))
    ->addArgument($_SERVER['APP_MYSQL_USER'])
    ->addArgument($_SERVER['APP_MYSQL_PASSWORD'])
    ->setPublic(false);

array_map(
    fn($class) => $container->autowire($class, $class)->setPublic(true),
    array(
        FileGeneratorCommand::class,
        FileParserCommand::class,
        App\Services\Generator::class,
        Parser::class,
        Reporter::class,
    )
);

array_map(
    fn($class) => $container->autowire($class, $class)
        ->setPublic(true)
        ->addTag(ProviderInterface::class),
    [
        DatabaseProvider::class,
        FileProvider::class
    ]
);
$container->setAlias(FileProviderInterface::class,
    FileProvider::class);
$fileRepos = [
    CityFileRepository::class => City::class,
    UserFileRepository::class => User::class
];
array_walk(
    $fileRepos,
    fn($entity, $repo) => $container->autowire($repo, $repo)
        ->setPublic(true)
        ->addTag(RepositoryInterface::class)
        ->setArguments([
            '$provider' => new Reference(
                FileProviderInterface::class
            ),
            '$entity' => $entity,
        ])
);

$container->setAlias(
    DatabaseProviderInterface::class, DatabaseProvider::class);
$dbRepos = [
    CityDatabaseRepository::class => City::class,
    UserDatabaseRepository::class => User::class,
];
array_walk(
    $dbRepos,
    fn($entity, $repo) => $container->autowire($repo, $repo)
        ->setPublic(true)
        ->addTag(RepositoryInterface::class)
        ->setArguments([
            '$provider' => new Reference(
                DatabaseProviderInterface::class
            ),
            '$entity' => $entity,
        ])
);

$cleaner = $container->autowire(
    Cleaner::class, Cleaner::class)->setPublic(true);
$repos = $container->findTaggedServiceIds(RepositoryInterface::class);
array_walk(
    $repos,
    fn($data, $class) => $cleaner->addMethodCall('addRepository',
        [new Reference($class)]),
);

$container->compile();
