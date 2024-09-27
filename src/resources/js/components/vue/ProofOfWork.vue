<template>
  <div>
    <button @click="getChallenge" :disabled="working">Get Challenge</button>
    <p v-if="challenge">Challenge: {{ challenge }}</p>
    <p v-if="difficulty !== null">Difficulty: {{ difficulty }}</p>
    <p v-if="proof">Proof: {{ proof }}</p>
    <p v-if="status">Status: {{ status }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      challenge: null,
      difficulty: null,
      proof: null,
      status: null,
      working: false,
    };
  },
  methods: {
    async getChallenge() {
      this.working = true;
      try {
        const response = await fetch('/api/pow/challenge');
        const data = await response.json();
        this.challenge = data.challenge;
        this.difficulty = data.difficulty;
        this.calculateProof();
      } catch (error) {
        console.error('Error fetching challenge:', error);
      }
    },
    async calculateProof() {
      let nonce = 0;
      while (!this.verifyProof(this.challenge, nonce.toString())) {
        nonce++;
      }
      this.proof = nonce.toString();
      this.verifyWithServer();
    },
    verifyProof(challenge, proof) {
      const hash = this.sha256(challenge + proof);
      return hash.startsWith('0'.repeat(this.difficulty));
    },
    async verifyWithServer() {
      try {
        const response = await fetch('/api/pow/verify', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ challenge: this.challenge, proof: this.proof }),
        });
        const data = await response.json();
        this.status = data.status;
      } catch (error) {
        console.error('Error verifying proof:', error);
      } finally {
        this.working = false;
      }
    },
    async sha256(message) {
      const msgBuffer = new TextEncoder().encode(message);
      const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
      const hashArray = Array.from(new Uint8Array(hashBuffer));
      return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    },
  },
};
</script>