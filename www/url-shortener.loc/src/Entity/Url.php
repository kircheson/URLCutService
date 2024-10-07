<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\Table(name: 'url')]
#[ORM\UniqueConstraint(name: 'unique_hash', columns: ['hash'])]
class Url
{
    public const EXPIRATION_INTERVAL = 'P1D';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\Column(type: 'string', length: 14)]
    private string $hash;

    #[ORM\Column(name: 'created_date', type: 'datetime_immutable')]
    private DateTimeImmutable $createdDate;

    #[ORM\Column(name: 'expired_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $expiredAt;

    #[ORM\Column(type: 'boolean')]
    private bool $sent = false;

    public function __construct()
    {
        /*         Я бы здесь делал иначе, если бы в ТЗ был эндпойнт по созданию URL-ов
                 Чтобы была возможность добавить createdDate на нужную дату и у нас не "поехал" expiredAt
                 Пример: Я хочу создать сущность URL-а на 10.09 с текущим кодом в Конструкторе expiredAt устанавливается на
                 <текущая дата> +24 часа, а надо было бы на 11.09. Для этого я бы вызвал установку expiredAt у setCreatedDate*/
        $date = new \DateTimeImmutable();
        $this->setCreatedDate($date);
        $this->setExpiredAt($date->add(new DateInterval(self::EXPIRATION_INTERVAL))); // Используем константу
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        $this->setHash(substr(md5($url), 0, 14));
        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function getCreatedDate(): ?DateTimeImmutable
    {
        return $this->createdDate;
    }

    public function setCreatedDate(DateTimeImmutable $createdDate): self
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    public function getExpiredAt(): ?DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(DateTimeImmutable $expiredAt): self
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }

    public function getSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;
        return $this;
    }
}
