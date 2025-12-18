<?php

namespace App\Filament\Resources\ThematicAreas\Pages;

use App\Filament\Resources\ThematicAreas\ThematicAreaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditThematicArea extends EditRecord
{
    protected static string $resource = ThematicAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
