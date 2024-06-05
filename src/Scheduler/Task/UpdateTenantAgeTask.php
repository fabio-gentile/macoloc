<?php

namespace App\Scheduler\Task;

use App\Service\UpdateTenantAgeService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 3 * * *')] // Run every day at 3:00am
readonly class UpdateTenantAgeTask
{
    public function __construct(private UpdateTenantAgeService $service) {}
    public function __invoke(): void
    {
        $this->service->updateTenantAge();
    }
}
