<?php

namespace App\Filament\Resources\ThematicAreas;

use App\Filament\Resources\ThematicAreas\Pages\CreateThematicArea;
use App\Filament\Resources\ThematicAreas\Pages\EditThematicArea;
use App\Filament\Resources\ThematicAreas\Pages\ListThematicAreas;
use App\Filament\Resources\ThematicAreas\Pages\ViewThematicArea;
use App\Filament\Resources\ThematicAreas\Schemas\ThematicAreaForm;
use App\Filament\Resources\ThematicAreas\Schemas\ThematicAreaInfolist;
use App\Filament\Resources\ThematicAreas\Tables\ThematicAreasTable;
use App\Models\ThematicArea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ThematicAreaResource extends Resource
{
    protected static ?string $model = ThematicArea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ThematicAreaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ThematicAreaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThematicAreasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThematicAreas::route('/'),
            'create' => CreateThematicArea::route('/create'),
            'view' => ViewThematicArea::route('/{record}'),
            'edit' => EditThematicArea::route('/{record}/edit'),
        ];
    }
}
