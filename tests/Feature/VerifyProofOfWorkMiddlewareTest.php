<?php

namespace Mohamedahmed01\LaravelPow\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Mohamedahmed01\LaravelPow\LaravelPowServiceProvider;
use Mohamedahmed01\LaravelPow\Middleware\VerifyProofOfWork;
use Mohamedahmed01\LaravelPow\Facades\Pow;

class VerifyProofOfWorkMiddlewareTest extends TestCase
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

    public function testMiddlewareWithValidProof()
    {
        $challenge = Pow::generateChallenge();
        $proof = $this->findValidProof($challenge);

        $response = $this->withHeaders([
            'X-PoW-Challenge' => $challenge,
            'X-PoW-Proof' => $proof,
        ])->get('/test-pow');

        $response->assertStatus(200);
    }

    public function testMiddlewareWithInvalidProof()
    {
        $challenge = Pow::generateChallenge();

        $response = $this->withHeaders([
            'X-PoW-Challenge' => $challenge,
            'X-PoW-Proof' => 'invalid_proof',
        ])->get('/test-pow');

        $response->assertStatus(403);
    }

    private function findValidProof($challenge)
    {
        $nonce = 0;
        while (!Pow::verifyProof($challenge, $nonce)) {
            $nonce++;
        }
        return $nonce;
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['router']->get('/test-pow', function () {
            return response()->json(['message' => 'success']);
        })->middleware(VerifyProofOfWork::class);
    }
}