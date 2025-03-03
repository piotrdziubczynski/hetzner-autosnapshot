<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

define('__ROOT_DIR__', dirname(__DIR__));

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use WRD\Hetzner\AutoSnapshot\Core\Config;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;
use WRD\Hetzner\AutoSnapshot\Service\Client;
use WRD\Hetzner\AutoSnapshot\Service\Image\ImageFetcher;
use WRD\Hetzner\AutoSnapshot\Service\ImageService;
use WRD\Hetzner\AutoSnapshot\Service\Server\Action\ImageCreator;
use WRD\Hetzner\AutoSnapshot\Service\Server\ServerFetcher;

require __ROOT_DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv();

$dotenv->usePutenv();
$dotenv->load(__ROOT_DIR__ . '/.env');

$config = Config::load();
$client = new Client($config);
$serializer = new Serializer([
    new ArrayDenormalizer(),
    new DateTimeNormalizer(),
    new BackedEnumNormalizer(),
    new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor()),
], [
    new JsonEncoder(),
]);
$logger = new Logger(Defaults::LOGGER_NAME, [new StreamHandler($config->getLoggerPath(), Level::Info)]);
$serverFetcher = new ServerFetcher($client, $serializer, $logger);
$servers = $serverFetcher->fetchByNames($config->getServers());

if (empty($servers?->servers ?? [])) {
    return;
}

$imageFetcher = new ImageFetcher($client, $serializer, $logger);
$images = $imageFetcher->fetchByPrefix($config->getImagePrefix());
$imageService = new ImageService($config);
$images = $imageService->assignImagesToServers($images);
$creator = new ImageCreator($config, $client, $serializer, $logger);
$now = new DateTimeImmutable();

foreach ($servers->servers as $server) {
    /** @var ImageModel[] $serverImages */
    $serverImages = $images?->images[$server->name] ?? [];
    /** @var null|ImageModel $image */
    $image = reset($serverImages) ?: null;
    $created = $creator->create($server, $now, $image);

    if (null === $created) {
        continue;
    }

    $message = sprintf(
        'Server: %s. The %s \'%s\' is %s.',
        $server->name,
        $created->image->type->value,
        $created->image->description,
        $created->image->status->value
    );

    $logger->info($message);
}
