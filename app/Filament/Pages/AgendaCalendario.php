<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgendaCalendarWidget;
use App\Models\Personal;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use UnitEnum;

class AgendaCalendario extends Page
{
    protected string $view = 'filament.pages.agenda-calendario';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static UnitEnum|string|null $navigationGroup = 'Agenda';

    protected static ?string $navigationLabel = 'Calendario';

    protected static ?string $title = 'Calendario';

    protected static ?int $navigationSort = 0;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * AG-14: filtro por Personal para el calendario del Administrador.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('responsable_id')
                    ->label('Filtrar por Personal')
                    ->options(fn (): array => Personal::query()
                        ->orderBy('nombre')
                        ->get()
                        ->mapWithKeys(fn (Personal $personal): array => [$personal->id => $personal->nombre_completo])
                        ->all())
                    ->searchable()
                    ->native(false)
                    ->placeholder('Todos')
                    ->live()
                    ->afterStateUpdated(fn (?string $state) => $this->dispatch(
                        'agenda-filtro-personal',
                        responsableId: filled($state) ? (int) $state : null,
                    )),
            ])
            ->statePath('data');
    }

    /**
     * @return array<class-string<Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [
            AgendaCalendarWidget::class,
        ];
    }
}
