<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Service\Image;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;
use WRD\Hetzner\AutoSnapshot\Core\Enum\ImageStatus;
use WRD\Hetzner\AutoSnapshot\Core\Enum\ImageType;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Image\ImagesModel;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;
use WRD\Hetzner\AutoSnapshot\Service\Client;
use WRD\Hetzner\AutoSnapshot\Service\FetcherInterface;

final readonly class ImageFetcher implements FetcherInterface
{
    private const string URL_FORMAT = '/images';

    public function __construct(
        private Client $client,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function fetchById(int $id): ?ImageModel
    {
        return null;
    }

    public function fetchAll(): ?ImagesModel
    {
        $options = [
            'query' => [
                'sort' => 'created:desc',
                'type' => ImageType::SNAPSHOT->value,
                'per_page' => Defaults::ITEMS_ON_PAGE,
            ],
        ];

        return $this->fetch($options);
    }

    public function fetchByPrefix(string $prefix): ?ImagesModel
    {
        $model = $this->fetchAll();

        if (null === $model) {
            return null;
        }

        return $this->filterByPrefix($model, $prefix);
    }

    public function fetchAllAvailable(): ?ImagesModel
    {
        $options = [
            'query' => [
                'sort' => 'created:desc',
                'type' => ImageType::SNAPSHOT->value,
                'status' => ImageStatus::AVAILABLE->value,
                'per_page' => Defaults::ITEMS_ON_PAGE,
            ],
        ];

        return $this->fetch($options);
    }

    public function fetchAvailableByPrefix(string $prefix): ?ImagesModel
    {
        $model = $this->fetchAllAvailable();

        if (null === $model) {
            return null;
        }

        return $this->filterByPrefix($model, $prefix);
    }

    private function fetch(array $options): ?ImagesModel
    {
        try {
            $response = $this->client->get(self::URL_FORMAT, $options);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                $this->logger->critical(sprintf('HttpClient error. Error code: %d', $response->getStatusCode()));

                return null;
            }

            return $this->serializer->deserialize($response->getContent(), ImagesModel::class, 'json');
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    public function filterByPrefix(ImagesModel $model, string $prefix): ImagesModel
    {
        if ('' === $prefix) {
            return $model;
        }

        $model->images = array_values(
            array_filter($model->images, static function (ImageModel $image) use ($prefix): bool {
                return str_starts_with($image->description, $prefix);
            })
        );

        return $model;
    }
}
