<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletTransferTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_transfer_funds_to_another_user()
    {
        $sender = User::factory()->create();
        $reciever = User::factory()->create();

        $sender->wallet->update(['balance' => 2000]);
        $response = $this->actingAs($sender)->post('wallet/transfer/payment', [
            'phone' => $reciever->phone,
            'amount' => 500,
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $sender->id,
            'balance' => 1500.00,
        ]);
        $fees_amount = (10 / 100) * 500;
        $amount_after_fees = 500 - $fees_amount;
        $this->assertDatabaseHas('wallets', [
            'user_id' => $reciever->id,
            'balance' => $amount_after_fees,
        ]);
    }
    /** @test */
    public function a_user_cannot_transfer_more_funds_than_they_have()
    {
        $sender = User::factory()->create();
        $reciever = User::factory()->create();

        $sender->wallet->update(['balance' => 300]);
        $response = $this->actingAs($sender)->post('/wallet/transfer/payment', [
            'phone' => $reciever->phone,
            'amount' => 500,
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('wallets', ['user_id' => $sender->id, 'balance' => 300]);
        $this->assertDatabaseHas('wallets', ['user_id' => $reciever->id, 'balance' => 0]);
    }
    /** @test */
    public function top_up_amount_must_be_valid()
    {
        $sender = User::factory()->create();
        $reciever = User::factory()->create();

        $sender->wallet->update(['balance' => 300]);
        $response = $this->actingAs($sender)->post('wallet/topup/payment', [
            'phone' => $reciever->phone,
            'amount' => -500,
        ]);
        $response->assertSessionHasErrors('amount');
        $response->assertStatus(302);
    }
}
