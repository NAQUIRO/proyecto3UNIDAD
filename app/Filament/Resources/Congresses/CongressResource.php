<?php

namespace App\Filament\Resources\Congresses;

use App\Filament\Resources\Congresses\Pages\CreateCongress;
use App\Filament\Resources\Congresses\Pages\EditCongress;
use App\Filament\Resources\Congresses\Pages\ListCongresses;
use App\Filament\Resources\Congresses\Pages\ViewCongress;
use App\Filament\Resources\Congresses\Schemas\CongressForm;
use App\Filament\Resources\Congresses\Schemas\CongressInfolist;
use App\Filament\Resources\Congresses\Tables\CongressesTable;
use App\Models\Congress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CongressResource extends Resource
{
    protected static ?string $model = Congress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CongressForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CongressInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CongressesTable::configure($table);
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
            'index' => ListCongresses::route('/'),
            'create' => CreateCongress::route('/create'),
            'view' => ViewCongress::route('/{record}'),
            'edit' => EditCongress::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
