<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\HttpClient\UriTemplateHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use WRD\Hetzner\AutoSnapshot\Core\Config;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;

final readonly class Client
{
    private HttpClientInterface $client;

    public function __construct(Config $config)
    {
        $options = (new HttpOptions())
            ->setBaseUri($config->getApiBaseUri())
            ->setAuthBearer($config->getApiToken())
            ->setHeader('Content-Type', Defaults::CONTENT_TYPE);

        $this->client = new UriTemplateHttpClient(HttpClient::create($options->toArray()));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        return $this->client->request(Request::METHOD_GET, $this->fixUrl($url), $options);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function post(string $url, array $options = []): ResponseInterface
    {
        return $this->client->request(Request::METHOD_POST, $this->fixUrl($url), $options);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function delete(string $url, array $options = []): ResponseInterface
    {
        return $this->client->request(Request::METHOD_DELETE, $this->fixUrl($url), $options);
    }

    private function fixUrl(string $url): string
    {
        return mb_strtolower(ltrim($url, '/'), 'UTF-8');
    }
}
