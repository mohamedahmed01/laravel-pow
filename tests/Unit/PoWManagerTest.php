<?php

namespace Mohamedahmed01\LaravelPow\Tests\Unit;

use Orchestra\Testbench\TestCase;
use Mohamedahmed01\LaravelPow\LaravelPowServiceProvider;
use Mohamedahmed01\LaravelPow\Middleware\VerifyProofOfWork;
use Mohamedahmed01\LaravelPow\Facades\Pow;


class PowManagerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [LaravelPowServiceProvider::class];
    }

    public function testGenerateChallenge()
    {
        $challenge = Pow::generateChallenge();
        $this->assertIsString($challenge);
        $this->assertEquals(32, strlen($challenge));
    }

    public function testVerifyProof()
    {
        $challenge = Pow::generateChallenge();
        $proof = $this->findValidProof($challenge);
        $this->assertTrue(Pow::verifyProof($challenge, $proof));
    }

    public function testInvalidProof()
    {
        $challenge = Pow::generateChallenge();
        $this->assertFalse(Pow::verifyProof($challenge, 'invalid_proof'));
    }

    private function findValidProof($challenge)
    {
        $nonce = 0;
        while (!Pow::verifyProof($challenge, $nonce)) {
            $nonce++;
        }
        return $nonce;
    }
}