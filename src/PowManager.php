<?php

namespace Mohamedahmed01\LaravelPow;

class PowManager
{
    protected $difficulty;

    public function __construct($difficulty)
    {
        $this->difficulty = $difficulty;
    }

    public function generateChallenge()
    {
        return bin2hex(random_bytes(16));
    }

    public function verifyProof($challenge, $proof)
    {
        $hash = hash('sha256', $challenge . $proof);
        return str_starts_with($hash, str_repeat('0', $this->difficulty));
    }
}