<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_registration_creates_record(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Jan Janssen')
            ->set('email', 'jan@example.com')
            ->set('telefoon', '0471234567')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('deelnameverzoeken', [
            'activiteit_id' => $activiteit->id,
            'naam' => 'Jan Janssen',
            'email' => 'jan@example.com',
            'status' => 'te_contacteren',
        ]);
    }

    public function test_registration_sends_two_emails(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Jan Janssen')
            ->set('email', 'jan@example.com')
            ->call('submit');

        Mail::assertSent(\App\Mail\RegistratieNotificatie::class);
        Mail::assertSent(\App\Mail\RegistratieBevestiging::class);
    }

    public function test_form_requires_naam_and_email(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->call('submit')
            ->assertHasErrors(['naam', 'email']);
    }

    public function test_form_shows_full_when_at_capacity(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'max_deelnemers' => 1,
        ]);
        Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->assertSee('Volzet');
    }

    public function test_honeypot_spam_field_blocks_submission(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Spammer')
            ->set('email', 'spam@example.com')
            ->set('honeypot', 'filled-by-bot')
            ->call('submit');

        $this->assertDatabaseCount('deelnameverzoeken', 0);
        Mail::assertNothingSent();
    }
}
