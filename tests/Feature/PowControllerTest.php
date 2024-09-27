<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Mohamedahmed01\LaravelPow\Facades\Pow;
use Illuminate\Support\Facades\Config;
use Mohamedahmed01\LaravelPow\LaravelPowServiceProvider;

class PowControllerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelPowServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Pow' => Pow::class,
        ];
    }

    public function test_challenge_endpoint_returns_challenge()
    {
        $response = $this->getJson('/api/pow/challenge');

        $response->assertStatus(200)
            ->assertJsonStructure(['challenge', 'difficulty']);
    }

    public function test_challenge_endpoint_returns_correct_difficulty()
    {
        Config::set('pow.difficulty', 5);

        $response = $this->getJson('/api/pow/challenge');

        $response->assertStatus(200)
            ->assertJson(['difficulty' => 5]);
    }

    public function test_verify_endpoint_success()
    {
        $challenge = 'test_challenge';
        $proof = 'valid_proof';

        Pow::shouldReceive('verifyProof')
            ->once()
            ->with($challenge, $proof)
            ->andReturn(true);

        $response = $this->postJson('/api/pow/verify', [
            'challenge' => $challenge,
            'proof' => $proof,
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    public function test_verify_endpoint_failure()
    {
        $challenge = 'test_challenge';
        $proof = 'invalid_proof';

        Pow::shouldReceive('verifyProof')
            ->once()
            ->with($challenge, $proof)
            ->andReturn(false);

        $response = $this->postJson('/api/pow/verify', [
            'challenge' => $challenge,
            'proof' => $proof,
        ]);

        $response->assertStatus(403)
            ->assertJson(['status' => 'failure']);
    }

    public function test_verify_endpoint_missing_parameters()
    {
        $response = $this->postJson('/api/pow/verify', []);

        $response->assertStatus(422);
    }
}