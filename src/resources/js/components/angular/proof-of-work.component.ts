import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-proof-of-work',
  template: `
    <div>
      <button (click)="getChallenge()" [disabled]="working">Get Challenge</button>
      <p *ngIf="challenge">Challenge: {{ challenge }}</p>
      <p *ngIf="difficulty !== null">Difficulty: {{ difficulty }}</p>
      <p *ngIf="proof">Proof: {{ proof }}</p>
      <p *ngIf="status">Status: {{ status }}</p>
    </div>
  `
})
export class ProofOfWorkComponent {
  challenge: string | null = null;
  difficulty: number | null = null;
  proof: string | null = null;
  status: string | null = null;
  working = false;

  constructor(private http: HttpClient) {}

  async getChallenge() {
    this.working = true;
    try {
      const response: any = await this.http.get('/api/pow/challenge').toPromise();
      this.challenge = response.challenge;
      this.difficulty = response.difficulty;
      this.calculateProof();
    } catch (error) {
      console.error('Error fetching challenge:', error);
    }
  }

  async calculateProof() {
    let nonce = 0;
    while (!this.verifyProof(this.challenge!, nonce.toString(), this.difficulty!)) {
      nonce++;
    }
    this.proof = nonce.toString();
    this.verifyWithServer();
  }

  verifyProof(challenge: string, proof: string, difficulty: number): boolean {
    const hash = this.sha256(challenge + proof);
    return hash.startsWith('0'.repeat(difficulty));
  }

  async verifyWithServer() {
    try {
      const response: any = await this.http.post('/api/pow/verify', {
        challenge: this.challenge,
        proof: this.proof
      }).toPromise();
      this.status = response.status;
    } catch (error) {
      console.error('Error verifying proof:', error);
    } finally {
      this.working = false;
    }
  }

  async sha256(message: string): Promise<string> {
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
  }
}