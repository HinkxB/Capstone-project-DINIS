<?php

namespace App\Jobs;

use App\Models\Person;
use App\Services\Firefly\FireflyIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnchorCitizenToFireflyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(
        public readonly Person $person
    ) {}

    /**
     * Execute the job. 
     * Laravel's service container will automatically inject the FireflyIntegrationService.
     */
    public function handle(FireflyIntegrationService $fireflyService): void
    {
        $fullName = $this->person->first_name . ' ' . $this->person->last_name;

        // Make the call using our dedicated service
        $transactionId = $fireflyService->anchorCitizen(
            $this->person->person_id,
            $this->person->nrc_number ?? 'PENDING',
            $fullName,
            $this->person->date_of_birth
        );

        Log::info('Citizen anchoring submitted to FireFly successfully', [
            'person_id' => $this->person->person_id,
            'firefly_tx_id' => $transactionId
        ]);
        
        // Note: We don't update the local database status here! 
        // We wait for the Webhook to confirm the transaction was successfully mined.
    }
}