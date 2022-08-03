<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

class EnrichDevice extends AbstractDTO
{
    protected string $publicKey;
    protected string $presharedKey;
    protected string $endpoint;
    protected string $allowedIps;
    protected int $latestHandshakeAt;
    protected int $transferRx;
    protected int $transferTx;
    protected int $persistentKeepalive;

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getPresharedKey(): string
    {
        return $this->presharedKey;
    }

    public function setPresharedKey(string $presharedKey): self
    {
        $this->presharedKey = $presharedKey;

        return $this;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getAllowedIps(): string
    {
        return $this->allowedIps;
    }

    public function setAllowedIps(string $allowedIps): self
    {
        $this->allowedIps = $allowedIps;

        return $this;
    }

    public function getLatestHandshakeAt(): int
    {
        return $this->latestHandshakeAt;
    }

    public function setLatestHandshakeAt(int $latestHandshakeAt): self
    {
        $this->latestHandshakeAt = $latestHandshakeAt;

        return $this;
    }

    public function getTransferRx(): int
    {
        return $this->transferRx;
    }

    public function setTransferRx(int $transferRx): self
    {
        $this->transferRx = $transferRx;

        return $this;
    }

    public function getTransferTx(): int
    {
        return $this->transferTx;
    }

    public function setTransferTx(int $transferTx): self
    {
        $this->transferTx = $transferTx;

        return $this;
    }

    public function isPersistentKeepalive(): int
    {
        return $this->persistentKeepalive;
    }

    public function setPersistentKeepalive(int $persistentKeepalive): self
    {
        $this->persistentKeepalive = $persistentKeepalive;

        return $this;
    }
}
