<?php

namespace App\Filament\Pages;

use App\Models\OverOnsContent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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

    protected static ?int $navigationSort = 3;

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
                Form::make([
                    Section::make('Jaarverslag')
                        ->description('Laat het PDF-veld leeg om het jaarverslag-blok op de Over ons-pagina te verbergen.')
                        ->schema([
                            Grid::make(['default' => 1, 'md' => 4])
                                ->schema([
                                    TextInput::make('jaarverslag_jaar')
                                        ->label('Jaar')
                                        ->numeric()
                                        ->minValue(2000)
                                        ->maxValue(2100)
                                        ->columnSpan(['md' => 1]),
                                    SpatieMediaLibraryFileUpload::make('jaarverslag')
                                        ->label('PDF-bestand')
                                        ->collection('jaarverslag')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(20480)
                                        ->columnSpan(['md' => 3]),
                                ]),
                        ]),
                    Section::make('Impactcijfers')
                        ->schema([
                            Grid::make(3)->schema([
                                $this->impactColumn(1, 'Bezoekers'),
                                $this->impactColumn(2, 'Maaltijden'),
                                $this->impactColumn(3, 'Activiteiten'),
                            ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Opslaan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    protected function impactColumn(int $n, string $label): Section
    {
        return Section::make($label)
            ->compact()
            ->schema([
                TextInput::make("impact_{$n}_aantal")
                    ->label('Aantal')
                    ->required()
                    ->maxLength(20),
                TextInput::make("impact_{$n}_omschrijving_nl")
                    ->label('Omschrijving (NL)')
                    ->required()
                    ->maxLength(120),
                TextInput::make("impact_{$n}_omschrijving_fr")
                    ->label('Omschrijving (FR)')
                    ->required()
                    ->maxLength(120),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = $this->getRecord();
        $record->fill($data);
        $record->save();

        Notification::make()
            ->success()
            ->title('Over ons-pagina bijgewerkt')
            ->send();
    }

    public function getRecord(): OverOnsContent
    {
        return $this->record ??= OverOnsContent::current();
    }
}
