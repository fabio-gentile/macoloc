<?php

namespace App\Scheduler\Task;

use App\Service\NewsletterService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 8 * * *')] // Run every day at 8:00am
readonly class SendNewsletterTask
{
    public function __construct(private NewsletterService $service) {}
    public function __invoke(): void
    {
        $this->service->sendNewsletter();
    }
}
