<?php

namespace App\Filament\Pages;

use App\Models\OverOnsContent;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageOverOnsContent extends Page
{
    protected string $view = 'filament.pages.manage-over-ons-content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Over ons';

    protected static string|UnitEnum|null $navigationGroup = 'Inhoud';

    protected static ?string $title = 'Over ons-pagina';

    protected static ?string $slug = 'over-ons';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    private ?OverOnsContent $record = null;

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sections will be added in Task 5.
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function getRecord(): OverOnsContent
    {
        return $this->record ??= OverOnsContent::current();
    }
}
