<?php

namespace Tests\Unit;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_admin_can_access_panel(): void
    {
        config(['auth.admin_emails' => ['admin@deharmonie.be', 'frederik@example.com']]);

        $user = User::factory()->create(['email' => 'admin@deharmonie.be']);

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_extra_admin_can_access_panel(): void
    {
        config(['auth.admin_emails' => ['admin@deharmonie.be', 'frederik@example.com']]);

        $user = User::factory()->create(['email' => 'frederik@example.com']);

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_unlisted_email_cannot_access_panel(): void
    {
        config(['auth.admin_emails' => ['admin@deharmonie.be']]);

        $user = User::factory()->create(['email' => 'random@example.com']);

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
    }
}
