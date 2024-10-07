<?php

namespace App\Command;

use App\Repository\UrlRepository;
use App\Service\UrlSenderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendUrlsCommand extends Command
{
    protected static $defaultName = 'app:send-urls';

    private $urlSenderService;
    /**
     * @var string
     */
    private $endpoint;

    public function __construct(UrlSenderService $urlSenderService, string $endpoint)
    {
        parent::__construct();
        $this->urlSenderService = $urlSenderService;
        $this->endpoint = $endpoint;
    }

    protected function configure()
    {
        $this->setDescription('Send new URLs to the specified endpoint.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->urlSenderService->sendNewUrls($this->endpoint);
            $output->writeln("New URLs sent successfully.");
        } catch (\Exception $e) {
            $output->writeln("Error: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}