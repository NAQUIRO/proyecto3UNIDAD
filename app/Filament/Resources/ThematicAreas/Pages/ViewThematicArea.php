<?php

namespace App\Filament\Resources\ThematicAreas\Pages;

use App\Filament\Resources\ThematicAreas\ThematicAreaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewThematicArea extends ViewRecord
{
    protected static string $resource = ThematicAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
