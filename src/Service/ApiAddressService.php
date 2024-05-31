<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiAddressService
{
    public function __construct(
        private HttpClientInterface $httpClient
    ){}


    /**
     * Get address from query
     * @param string $query
     * @return array|null
     */
    public function getAddress(string $query): ?array
    {
        $response = $this->httpClient->request(
            'GET',
            'https://api-adresse.data.gouv.fr/search/?q=&type=municipality&autocomplete=1',
            [
                'query' => [
                    'q' => $query,
                    'limit' => 5,
                ],
            ]
        );

        $responseArray = $response->toArray();
        $results = array_map(function ($feature) {
            return [
                'label' => $feature['properties']['label'] ?? null,
            ];
        }, $responseArray['features']);

        return $results;
    }
}
