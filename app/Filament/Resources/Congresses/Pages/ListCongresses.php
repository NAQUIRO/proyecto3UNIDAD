<?php

namespace App\Filament\Resources\Congresses\Pages;

use App\Filament\Resources\Congresses\CongressResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCongresses extends ListRecords
{
    protected static string $resource = CongressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
