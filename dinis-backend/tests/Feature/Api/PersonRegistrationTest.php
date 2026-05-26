<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;

class PersonRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_successfully_registers_via_api()
    {
        Queue::fake();
        Schema::disableForeignKeyConstraints();

        $response = $this->postJson('/api/v1/citizens/register', [
            'first_name' => 'Jane',
            'last_name' => 'Phiri',
            'sex_at_birth' => 'F',
            'date_of_birth' => '1995-05-15',
            'birth_country_id' => 1,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true);
    }
}