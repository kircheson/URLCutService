<?php

namespace App\DataFixtures;

use App\Entity\Url;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UrlFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testUrls = [
            ['url' => 'https://example.com', 'hash' => 'hash1'],
            ['url' => 'https://another-example.com', 'hash' => 'hash2'],
            ['url' => 'https://yetanother-example.com', 'hash' => 'hash3'],
            ['url' => 'https://test-url.com', 'hash' => 'hash4'],
            ['url' => 'https://sample-url.com', 'hash' => 'hash5'],
        ];

        foreach ($testUrls as $data) {
            $url = new Url();
            $url->setUrl($data['url']);
            $url->setHash($data['hash']);
            $url->setCreatedDate(new \DateTimeImmutable());
            $url->setSent(false); // Устанавливаем поле sent в false

            $manager->persist($url);
        }

        $manager->flush();
    }
}