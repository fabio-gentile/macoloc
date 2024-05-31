<?php

namespace App\Twig\Components;

use App\Service\ApiAddressService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class SearchInput
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private ApiAddressService $apiAddressService)
    {
    }

    public function getAddress() :?array
    {
        if (empty($this->query) || strlen($this->query) < 3) {
            return null;
        }

        return $this->apiAddressService->getAddress($this->query);
    }
}
