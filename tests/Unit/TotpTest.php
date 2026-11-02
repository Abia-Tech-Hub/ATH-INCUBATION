<?php

namespace Tests\Unit;

use App\Support\Totp;
use Tests\TestCase;

class TotpTest extends TestCase
{
    public function test_it_matches_the_rfc_4226_vectors(): void
    {
        $secret = '12345678901234567890';

        $this->assertSame('755224', Totp::hotp($secret, 0));
        $this->assertSame('287082', Totp::hotp($secret, 1));
    }

    public function test_base32_round_trips(): void
    {
        $secret = random_bytes(20);

        $this->assertSame($secret, Totp::base32Decode(Totp::base32Encode($secret)));
    }

    public function test_verify_accepts_the_current_step_and_refuses_a_replay(): void
    {
        $secret = '12345678901234567890';
        $base32 = Totp::base32Encode($secret);
        $now = 1_700_000_000;
        $step = intdiv($now, 30);
        $code = Totp::hotp($secret, $step);

        $this->assertSame($step, Totp::verify($base32, $code, 0, $now));
        $this->assertNull(Totp::verify($base32, $code, $step, $now), 'a used step cannot be replayed');
        $this->assertNull(Totp::verify($base32, '000000', 0, $now));
        $this->assertNull(Totp::verify($base32, 'abcdef', 0, $now));
    }
}
