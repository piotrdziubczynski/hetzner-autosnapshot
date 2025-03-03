<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core;

use RuntimeException;
use WRD\Hetzner\AutoSnapshot\Core\Enum\Frequency;

final class Config
{
    private string $apiToken;
    /** @var string[] */
    private array $servers;
    private int $imageBackupMinimum;
    private bool $imageDeleteUnassign;
    private string $imagePrefix;
    private string $apiBaseUri;
    private string $apiVersion;
    private string $loggerPath;
    private Frequency $frequencyBackup;
    private Frequency $frequencyDeleting;

    private function __construct()
    {
        $this->setToken();
        $this->setServers();
        $this->setImageBackupMinimum();
        $this->setImageDeleteUnassign();
        $this->setImagePrefix();
        $this->setFrequencyBackup();
        $this->setFrequencyDeleting();
        $this->setApiVersion();

        $this->apiBaseUri = mb_strtolower(sprintf(Defaults::API_BASE_URI_FORMAT, $this->apiVersion));
        $this->loggerPath = Defaults::LOGGER_PATH;
    }

    public static function load(): self
    {
        return new self();
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function getServers(): array
    {
        return $this->servers;
    }

    public function getImageBackupMinimum(): int
    {
        return $this->imageBackupMinimum;
    }

    public function isImageDeleteUnassign(): bool
    {
        return $this->imageDeleteUnassign;
    }

    public function getImagePrefix(): string
    {
        return $this->imagePrefix;
    }

    public function getApiBaseUri(): string
    {
        return $this->apiBaseUri;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    public function getLoggerPath(): string
    {
        return $this->loggerPath;
    }

    public function getFrequencyBackup(): Frequency
    {
        return $this->frequencyBackup;
    }

    public function getFrequencyDeleting(): Frequency
    {
        return $this->frequencyDeleting;
    }

    /**
     * @throws RuntimeException
     */
    private function setToken(): void
    {
        /** @var false|string $token */
        $token = getenv('API_TOKEN');

        if (false === $token || '' === $token) {
            throw new RuntimeException('API_TOKEN env variable is not set.');
        }

        $this->apiToken = $token;
    }

    private function setServers(): void
    {
        /** @var false|string $servers */
        $servers = getenv('SERVERS_LIST');

        if (false === $servers || '' === $servers) {
            throw new RuntimeException('SERVERS_LIST env variable is not set. Put at least one name.');
        }

        $this->servers = explode('|', $servers);
    }

    private function setImageBackupMinimum(): void
    {
        $backupMinimum = (int)getenv('IMAGE_BACKUP_MINIMUM');

        if (0 > $backupMinimum) {
            $backupMinimum = Defaults::IMAGE_BACKUP_MINIMUM;
        }

        $this->imageBackupMinimum = $backupMinimum;
    }

    private function setImageDeleteUnassign(): void
    {
        /** @var false|string $deleteUnassign */
        $deleteUnassign = getenv('IMAGE_DELETE_UNASSIGN');

        if (false === $deleteUnassign || '' === $deleteUnassign) {
            $deleteUnassign = null;
        }

        $this->imageDeleteUnassign = (bool)(int)($deleteUnassign ?? Defaults::IMAGE_DELETE_UNASSIGN);
    }

    private function setImagePrefix(): void
    {
        /** @var false|string $imagePrefix */
        $imagePrefix = getenv('IMAGE_PREFIX');

        if (false === $imagePrefix || '' === $imagePrefix) {
            $imagePrefix = null;
        }

        $this->imagePrefix = $imagePrefix ?? Defaults::IMAGE_PREFIX;
    }

    private function setFrequencyBackup(): void
    {
        /** @var false|string $frequencyBackup */
        $frequencyBackup = getenv('FREQUENCY_BACKUP');

        if (false === $frequencyBackup) {
            $frequencyBackup = '';
        }

        $this->frequencyBackup = Frequency::tryFrom($frequencyBackup) ?? Frequency::from(Defaults::FREQUENCY_BACKUP);
    }

    private function setFrequencyDeleting(): void
    {
        /** @var false|string $frequencyDeleting */
        $frequencyDeleting = getenv('FREQUENCY_DELETING');

        if (false === $frequencyDeleting) {
            $frequencyDeleting = '';
        }

        $this->frequencyDeleting = Frequency::tryFrom($frequencyDeleting) ?? Frequency::from(
            Defaults::FREQUENCY_DELETING
        );
    }

    private function setApiVersion(): void
    {
        /** @var false|string $apiVersion */
        $apiVersion = getenv('API_VERSION');

        if (false === $apiVersion || '' === $apiVersion) {
            $apiVersion = Defaults::API_VERSION;
        }

        $this->apiVersion = mb_strtolower($apiVersion);
    }
}
