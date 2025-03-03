<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Service\Server;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;
use WRD\Hetzner\AutoSnapshot\Core\Enum\ServerStatus;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ServerModel;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Server\ServersModel;
use WRD\Hetzner\AutoSnapshot\Service\Client;
use WRD\Hetzner\AutoSnapshot\Service\FetcherInterface;

final readonly class ServerFetcher implements FetcherInterface
{
    private const string URL_FORMAT = '/servers';

    public function __construct(
        private Client $client,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function fetchById(int $id): ?ServerModel
    {
        return null;
    }

    public function fetchAll(): ?ServersModel
    {
        $options = [
            'query' => [
                'status' => ServerStatus::RUNNING->value,
                'per_page' => Defaults::ITEMS_ON_PAGE,
            ],
        ];

        return $this->fetch($options);
    }

    /**
     * @param string[] $names
     */
    public function fetchByNames(array $names): ?ServersModel
    {
        $model = $this->fetchAll();

        if (null === $model) {
            return null;
        }

        if (empty($names)) {
            return $model;
        }

        $model->servers = array_values(
            array_filter($model->servers, static function (ServerModel $server) use ($names): bool {
                return in_array($server->name, $names, true);
            })
        );

        return $model;
    }

    private function fetch(array $options): ?ServersModel
    {
        try {
            $response = $this->client->get(self::URL_FORMAT, $options);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                $this->logger->critical(sprintf('HttpClient error. Error code: %d', $response->getStatusCode()));

                return null;
            }

            return $this->serializer->deserialize($response->getContent(), ServersModel::class, 'json');
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}
