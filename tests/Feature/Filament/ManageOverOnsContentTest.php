<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ManageOverOnsContent;
use App\Models\OverOnsContent;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ManageOverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_page_renders_for_authenticated_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/over-ons');

        $response->assertStatus(200);
    }

    public function test_page_redirects_guest_to_login(): void
    {
        $response = $this->get('/admin/over-ons');

        $response->assertRedirect('/admin/login');
    }

    public function test_form_is_prefilled_with_existing_record(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create([
            'jaarverslag_jaar' => 2026,
            'impact_1_aantal' => '321',
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->assertFormSet([
                'jaarverslag_jaar' => 2026,
                'impact_1_aantal' => '321',
            ]);
    }

    public function test_admin_can_update_impact_stats_and_jaarverslag_year(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'jaarverslag_jaar' => 2027,
                'impact_1_aantal' => '300',
                'impact_1_omschrijving_nl' => 'NL stat 1',
                'impact_1_omschrijving_fr' => 'FR stat 1',
                'impact_2_aantal' => '5000',
                'impact_2_omschrijving_nl' => 'NL stat 2',
                'impact_2_omschrijving_fr' => 'FR stat 2',
                'impact_3_aantal' => '70+',
                'impact_3_omschrijving_nl' => 'NL stat 3',
                'impact_3_omschrijving_fr' => 'FR stat 3',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('over_ons_content', [
            'id' => 1,
            'jaarverslag_jaar' => 2027,
            'impact_1_aantal' => '300',
            'impact_2_aantal' => '5000',
            'impact_3_aantal' => '70+',
            'impact_3_omschrijving_fr' => 'FR stat 3',
        ]);
    }

    public function test_required_fields_block_save_when_blank(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'impact_1_aantal' => '',
                'impact_1_omschrijving_nl' => '',
            ])
            ->call('save')
            ->assertHasFormErrors([
                'impact_1_aantal' => 'required',
                'impact_1_omschrijving_nl' => 'required',
            ]);
    }

    public function test_admin_can_upload_pdf_to_jaarverslag_collection(): void
    {
        Storage::fake('public');
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'jaarverslag' => [UploadedFile::fake()->createWithContent('verslag.pdf', "%PDF-1.4\nfake")],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(1, OverOnsContent::current()->getMedia('jaarverslag')->count());
        $this->assertNotNull(OverOnsContent::current()->getJaarverslagUrl());
    }

    public function test_uploading_a_new_pdf_replaces_the_previous_one(): void
    {
        Storage::fake('public');
        $this->seed(AdminUserSeeder::class);
        $content = OverOnsContent::factory()->create();
        $content->addMedia(UploadedFile::fake()->createWithContent('first.pdf', "%PDF-1.4\nfake"))
            ->toMediaCollection('jaarverslag');
        $this->assertSame(1, $content->fresh()->getMedia('jaarverslag')->count());

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'jaarverslag' => [UploadedFile::fake()->createWithContent('second.pdf', "%PDF-1.4\nfake")],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $media = OverOnsContent::current()->getMedia('jaarverslag');
        $this->assertSame(1, $media->count());
        $this->assertSame('second', $media->first()->name);
    }

    public function test_validation_rejects_non_pdf_uploads(): void
    {
        Storage::fake('public');
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'jaarverslag' => [UploadedFile::fake()->image('not-a-pdf.jpg')],
            ])
            ->call('save')
            ->assertHasFormErrors(['jaarverslag']);
    }
}
