<?php

namespace Mohamedahmed01\LaravelPow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mohamedahmed01\LaravelPow\Facades\Pow;
use Illuminate\Support\Facades\Config;
use Mohamedahmed01\LaravelPow\Http\Requests\VerifyPowRequest;

class PowController extends Controller
{

    public function getChallenge()
    {
        $challenge = Pow::generateChallenge();
        $difficulty = Config::get('pow.difficulty', 4);
        
        return response()->json([
            'challenge' => $challenge,
            'difficulty' => $difficulty,
        ]);
    }


    public function verify(VerifyPowRequest $request)
    {
        $challenge = $request->input('challenge');
        $proof = $request->input('proof');

        if (Pow::verifyProof($challenge, $proof)) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'failure'], 403);
    }
}