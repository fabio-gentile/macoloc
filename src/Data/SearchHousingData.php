<?php

namespace App\Data;

class SearchHousingData {
    public ?array $house_type = null;
    public ?array $commodity = null;
    public ?array $numberOfRooms = null;
    public ?array $disponibility = null;
    public ?string $min_price = null;
    public ?string $max_price = null;
    public ?string $postcode = null;
    public ?string $city = null;
    public int $page = 1;
}
