<?php

namespace Tests\Feature;

use App\Http\Enum\TransactionTypeEnum;
use App\Models\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WalletTopUpTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_top_up_their_wallet()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('wallet/topup/payment', [
            'amount' => 1000,
            'stripeToken' => 'tok_visa',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 1000,
        ]);
        $this->assertDatabaseHas('transaction_histories', [
            'amount' => 1000,
            'type' => TransactionTypeEnum::TOPUP,
            'user_id' => $user->id,
        ]);
    }
    /** @test */
    public function top_up_amount_must_be_valid()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('wallet/topup/payment', [
            'amount' => -1000,
            'stripeToken' => 'tok_visa',
        ]);
        $response->assertSessionHasErrors('amount');
        $response->assertStatus(302);
    }
}
