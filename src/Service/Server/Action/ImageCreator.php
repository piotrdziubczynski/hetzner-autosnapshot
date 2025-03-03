<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Service\Server\Action;

use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use WRD\Hetzner\AutoSnapshot\Core\Config;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;
use WRD\Hetzner\AutoSnapshot\Core\Enum\ImageType;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ServerModel;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Server\CreateImageModel;
use WRD\Hetzner\AutoSnapshot\Service\Client;

final readonly class ImageCreator
{
    private const string URL_FORMAT = '/servers/{id}/actions/create_image';

    public function __construct(
        private Config $config,
        private Client $client,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function create(ServerModel $server, DateTimeInterface $now, ?ImageModel $latestImage): ?CreateImageModel
    {
        /** @var null|DateTimeInterface $imageCreated */
        $imageCreated = $latestImage?->created->modify('midnight') ?: null;
        $backup = $this->config->getFrequencyBackup();
        /** @var null|DateTimeInterface $youngerThan */
        $youngerThan = $now->modify(sprintf('midnight -%s', $backup->period())) ?: null;

        if ($imageCreated > $youngerThan) {
            $this->logger->notice(
                sprintf('A %s image for \'%s\' server already created.', $backup->value, $server->name)
            );

            return null;
        }

        $description = sprintf(
            Defaults::IMAGE_NAME_FORMAT,
            $this->config->getImagePrefix(),
            $server->name,
            $now->format(Defaults::DATETIME_FORMAT)
        );
        $payload = [
            'description' => $description,
            'type' => ImageType::SNAPSHOT->value,
        ];

        try {
            $response = $this->client->post(self::URL_FORMAT, [
                'vars' => ['id' => $server->id],
                'body' => $this->serializer->serialize($payload, 'json'),
            ]);

            if (Response::HTTP_CREATED !== $response->getStatusCode()) {
                $this->logger->critical(sprintf('HttpClient error. Error code: %d', $response->getStatusCode()));

                return null;
            }

            $model = $this->serializer->deserialize($response->getContent(), CreateImageModel::class, 'json');

            if (!$model->action->status->isSuccess()) {
                $this->logger->error($model->action->error->message);

                return null;
            }

            return $model;
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}
