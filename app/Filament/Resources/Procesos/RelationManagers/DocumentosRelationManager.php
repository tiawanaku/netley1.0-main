<?php

namespace App\Filament\Resources\Procesos\RelationManagers;

use App\Enums\CategoriaDocumento;
use App\Enums\OrigenDocumento;
use App\Models\Personal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Select::make('categoria')
                    ->options(CategoriaDocumento::class)
                    ->default(CategoriaDocumento::Otro)
                    ->required()
                    ->native(false),
                Select::make('personal_id')
                    ->label('Subido por')
                    ->relationship('personal', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                    ->searchable(['nombre', 'apellidos'])
                    ->preload(),
                FileUpload::make('archivo')
                    ->required()
                    ->disk('public')
                    ->directory('procesos/documentos')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/png',
                        'image/jpeg',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('categoria')
                    ->badge(),
                TextColumn::make('origen')
                    ->badge(),
                TextColumn::make('personal.nombre_completo')
                    ->label('Subido por')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('categoria')
                    ->options(CategoriaDocumento::class),
                SelectFilter::make('origen')
                    ->options(OrigenDocumento::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['origen'] = OrigenDocumento::Staff->value;

                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->url(fn ($record): string => Storage::disk('public')->url($record->archivo))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
