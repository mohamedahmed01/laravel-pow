<?php

namespace Mohamedahmed01\LaravelPow\Middleware;

use Mohamedahmed01\LaravelPow\Facades\Pow;

use Closure;

class VerifyProofOfWork
{
    public function handle($request, Closure $next)
    {
        $challenge = $request->header('X-PoW-Challenge');
        $proof = $request->header('X-PoW-Proof');

        if (!$challenge || !$proof || !Pow::verifyProof($challenge, $proof)) {
            return response()->json(['error' => 'Invalid Proof of Work'], 403);
        }

        return $next($request);
    }
}