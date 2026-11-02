<?php

namespace Tests\Unit;

use App\Support\Masking;
use Tests\TestCase;

class MaskingTest extends TestCase
{
    public function test_it_hides_contact_details_for_staff(): void
    {
        $this->assertSame('j•••••@example.com', Masking::email('jordan@example.com'));
        $this->assertSame('••• ••• 123', Masking::phone('+44 7700 900123'));

        $row = Masking::forRole('STAFF', ['email' => 'jordan@example.com', 'phone' => '+44 7700 900123']);

        $this->assertSame('j•••••@example.com', $row['email']);
        $this->assertSame('••• ••• 123', $row['phone']);
    }

    public function test_owners_and_admins_see_full_details(): void
    {
        foreach (['OWNER', 'ADMIN'] as $role) {
            $row = Masking::forRole($role, ['email' => 'jordan@example.com', 'phone' => '+44 7700 900123']);
            $this->assertSame('jordan@example.com', $row['email']);
            $this->assertSame('+44 7700 900123', $row['phone']);
        }
    }
}
