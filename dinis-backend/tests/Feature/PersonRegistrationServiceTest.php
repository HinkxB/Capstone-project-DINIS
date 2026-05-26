<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Identity\PersonRegistrationService;
use App\DTOs\Person\RegisterPersonDTO;
use App\Jobs\AnchorCitizenToFireflyJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class PersonRegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_registers_person_and_dispatches_blockchain_job()
    {
        // 1. Setup mocks
        Queue::fake();
        Schema::disableForeignKeyConstraints();

        $service = app(PersonRegistrationService::class);

        // 2. Prepare Data
        $dto = new RegisterPersonDTO(
            firstName: 'John',
            lastName: 'Banda',
            dateOfBirth: Carbon::parse('1990-01-01'),
            sexAtBirth: 'M',
            birthCountryId: 1,
            placeOfBirthLocationId: 1,
            middleName: 'Mwansa'
        );

        // 3. Execute
        $person = $service->registerBasePerson($dto, 1);

        Schema::enableForeignKeyConstraints();

        // 4. Assertions
        $this->assertNotNull($person);
        $this->assertEquals('John', $person->first_name);
        $this->assertDatabaseHas('citizen_nrc_record', [
            'first_name' => 'John',
            'last_name' => 'Banda'
        ]);
        
        // Ensure UIN was generated and saved
        $this->assertNotNull($person->uin);
        
        // Ensure Job was dispatched
        Queue::assertPushed(AnchorCitizenToFireflyJob::class);
    }
}