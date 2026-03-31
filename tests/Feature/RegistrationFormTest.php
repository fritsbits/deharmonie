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

        Mail::assertQueued(RegistratieNotificatie::class);
        Mail::assertSent(RegistratieBevestiging::class);
    }

    public function test_form_requires_naam_and_email(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->call('submit')
            ->assertHasErrors(['naam', 'email']);
    }

    public function test_activity_detail_page_shows_contact_info_not_form(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        $response = $this->get(route('nl.activiteiten.show', $activiteit->slug));

        $response->assertStatus(200);
        $response->assertDontSee(__('forms.submit'));
        $response->assertSee('02');
        $response->assertSee('info@deharmonie.be');
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

    public function test_success_state_shows_activity_details(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'titel_nl' => 'Koken met kruiden',
            'locatie' => 'De Harmonie',
        ]);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Marie Dupont')
            ->set('email', 'marie@example.com')
            ->call('submit')
            ->assertSee('Koken met kruiden')
            ->assertSee('De Harmonie')
            ->assertSee('Je bent ingeschreven');
    }

    public function test_confirmation_email_has_confirmed_tone_and_activity_details(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'titel_nl' => 'Koken met kruiden',
            'titel_fr' => 'Cuisine aux herbes',
            'locatie' => 'De Harmonie',
        ]);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Marie Dupont')
            ->set('email', 'marie@example.com')
            ->call('submit');

        $verzoek = Deelnameverzoek::where('email', 'marie@example.com')->first();
        $mail = new RegistratieBevestiging($verzoek, $activiteit, 'nl');
        $html = $mail->render();

        $this->assertStringContainsString('ingeschreven', $html);
        $this->assertStringContainsString('Koken met kruiden', $html);
        $this->assertStringContainsString('De Harmonie', $html);
        $this->assertStringContainsString('02 203 28 48', $html);
        $this->assertStringNotContainsString('nemen snel contact', $html);
    }

    public function test_staff_notification_has_reply_to_and_date_in_subject(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'titel_nl' => 'Koken met kruiden',
            'datum' => '2026-04-18',
        ]);

        Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Marie Dupont')
            ->set('email', 'marie@example.com')
            ->call('submit');

        Mail::assertQueued(RegistratieNotificatie::class, function (RegistratieNotificatie $mail): bool {
            $envelope = $mail->envelope();
            $hasReplyTo = collect($envelope->replyTo)->contains(
                fn ($address) => $address->address === 'marie@example.com'
            );
            $hasDate = str_contains($envelope->subject, 'april');

            return $hasReplyTo && $hasDate;
        });
    }
}
