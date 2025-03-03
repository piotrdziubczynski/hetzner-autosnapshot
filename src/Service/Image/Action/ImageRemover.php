<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Service\Image\Action;

use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use WRD\Hetzner\AutoSnapshot\Core\Config;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;
use WRD\Hetzner\AutoSnapshot\Service\Client;

final readonly class ImageRemover
{
    private const string URL_FORMAT = '/images/{id}';

    public function __construct(
        private Config $config,
        private Client $client,
        private LoggerInterface $logger,
    ) {
    }

    public function delete(ImageModel $image, ?DateTimeInterface $now = null): bool
    {
        /** @var null|DateTimeInterface $imageCreated */
        $imageCreated = $image->created->modify('midnight') ?: null;
        /** @var null|DateTimeInterface $olderThan */
        $olderThan = $now?->modify(sprintf('midnight -%s', $this->config->getFrequencyDeleting()->period())) ?: null;

        if ($imageCreated > $olderThan) {
            return false;
        }

        try {
            $response = $this->client->delete(self::URL_FORMAT, [
                'vars' => ['id' => $image->id],
            ]);

            if (Response::HTTP_NO_CONTENT !== $response->getStatusCode()) {
                $this->logger->critical(sprintf('HttpClient error. Error code: %d', $response->getStatusCode()));

                return false;
            }

            return true;
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }
}
