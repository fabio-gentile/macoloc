<?php

namespace App\Data;

class SearchTenantData {
    public ?string $gender = null;
    public ?array $activity = null;
    public ?string $min_age = null;
    public ?string $max_age = null;
    public ?string $min_price = null;
    public ?string $max_price = null;
    public ?string $city = null;
    public int $page = 1;
}
