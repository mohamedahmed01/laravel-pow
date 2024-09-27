import React, { useState, useEffect } from 'react';

const ProofOfWork = () => {
  const [challenge, setChallenge] = useState(null);
  const [difficulty, setDifficulty] = useState(null);
  const [proof, setProof] = useState(null);
  const [status, setStatus] = useState(null);
  const [working, setWorking] = useState(false);

  const getChallenge = async () => {
    setWorking(true);
    try {
      const response = await fetch('/api/pow/challenge');
      const data = await response.json();
      setChallenge(data.challenge);
      setDifficulty(data.difficulty);
      calculateProof(data.challenge, data.difficulty);
    } catch (error) {
      console.error('Error fetching challenge:', error);
    }
  };

  const calculateProof = (challenge, difficulty) => {
    let nonce = 0;
    while (!verifyProof(challenge, nonce.toString(), difficulty)) {
      nonce++;
    }
    setProof(nonce.toString());
    verifyWithServer(challenge, nonce.toString());
  };

  const verifyProof = (challenge, proof, difficulty) => {
    const hash = sha256(challenge + proof);
    return hash.startsWith('0'.repeat(difficulty));
  };

  const verifyWithServer = async (challenge, proof) => {
    try {
      const response = await fetch('/api/pow/verify', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ challenge, proof }),
      });
      const data = await response.json();
      setStatus(data.status);
    } catch (error) {
      console.error('Error verifying proof:', error);
    } finally {
      setWorking(false);
    }
  };

  const sha256 = async (message) => {
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
  };

  return (
    <div>
      <button onClick={getChallenge} disabled={working}>Get Challenge</button>
      {challenge && <p>Challenge: {challenge}</p>}
      {difficulty !== null && <p>Difficulty: {difficulty}</p>}
      {proof && <p>Proof: {proof}</p>}
      {status && <p>Status: {status}</p>}
    </div>
  );
};

export default ProofOfWork;