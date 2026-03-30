<?php

namespace Tests\Feature;

use App\Livewire\RegistrationForm;
use App\Mail\RegistratieBevestiging;
use App\Mail\RegistratieNotificatie;
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

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Jan Janssen')
            ->set('email', 'jan@example.com')
            ->set('telefoon', '0471234567')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('deelnameverzoeken', [
            'activiteit_id' => $activiteit->id,
            'naam' => 'Jan Janssen',
            'email' => 'jan@example.com',
        ]);
    }

    public function test_registration_sends_two_emails(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Jan Janssen')
            ->set('email', 'jan@example.com')
            ->call('submit');

        Mail::assertSent(RegistratieNotificatie::class);
        Mail::assertSent(RegistratieBevestiging::class);
    }

    public function test_form_requires_naam_and_email(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->call('submit')
            ->assertHasErrors(['naam', 'email']);
    }

    public function test_form_still_shows_when_at_capacity(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'max_deelnemers' => 1,
        ]);
        Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

        // Capacity is not surfaced to users — form still renders, submission silently stops
        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->assertDontSee('Volzet')
            ->assertSee(__('forms.submit'));
    }

    public function test_honeypot_spam_field_blocks_submission(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Spammer')
            ->set('email', 'spam@example.com')
            ->set('honeypot', 'filled-by-bot')
            ->call('submit');

        $this->assertDatabaseCount('deelnameverzoeken', 0);
        Mail::assertNothingSent();
    }
}
