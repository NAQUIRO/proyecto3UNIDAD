<?php

namespace App\Filament\Resources\ThematicAreas\Pages;

use App\Filament\Resources\ThematicAreas\ThematicAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListThematicAreas extends ListRecords
{
    protected static string $resource = ThematicAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
