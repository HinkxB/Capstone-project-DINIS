<?php

namespace App\Services\Firefly;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FireflyIntegrationService
{
    private string $baseUrl;
    private string $apiName;

    public function __construct()
    {
        // Pull configuration from Laravel's config system (defined in .env)
        // Defaults are provided for your local sandbox
        $this->baseUrl = config('services.firefly.base_url', 'http://127.0.0.1:5050/api/v1/namespaces/default');
        $this->apiName = config('services.firefly.api_name', 'zambiaIdentity');
    }

    /**
     * Core method to invoke any chaincode function via FireFly's custom API.
     * Keeps the service DRY (Don't Repeat Yourself).
     *
     * @param string $method The smart contract function name
     * @param array $input The arguments payload
     * @return array The JSON response from FireFly
     * @throws Exception
     */
    protected function invoke(string $method, array $input): array
    {
        $endpoint = "{$this->baseUrl}/apis/{$this->apiName}/invoke/{$method}";

        // We use a timeout so background jobs don't hang indefinitely if Docker is stuck
        $response = Http::timeout(15)->post($endpoint, [
            'input' => $input
        ]);

        if ($response->failed()) {
            Log::error("FireFly Invocation Failed for method [{$method}]", [
                'endpoint' => $endpoint,
                'input' => $input,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            throw new Exception("Blockchain integration failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Anchors a new citizen record to the distributed ledger.
     *
     * @return string The FireFly Transaction ID
     */
    public function anchorCitizen(string $personId, string $nrcNumber, string $fullName, string $dateOfBirth): string
    {
        $result = $this->invoke('AnchorCitizen', [
            'personId' => $personId,
            'nrcNumber' => $nrcNumber,
            'fullName' => $fullName,
            'dateOfBirth' => $dateOfBirth
        ]);

        return $result['id'] ?? throw new Exception("FireFly response missing transaction ID.");
    }

    /**
     * Anchors a marriage record to the distributed ledger.
     * (Based on your Schema.txt)
     */
    public function anchorMarriage(string $marriageId, string $wifePersonId, string $husbandPersonId, string $marriageDate): string
    {
        $result = $this->invoke('AnchorMarriage', [
            'marriageId' => $marriageId,
            'wifePersonId' => $wifePersonId,
            'husbandPersonId' => $husbandPersonId,
            'marriageDate' => $marriageDate
        ]);

        return $result['id'] ?? throw new Exception("FireFly response missing transaction ID.");
    }
}