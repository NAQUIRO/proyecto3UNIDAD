<?php

namespace App\Filament\Resources\Congresses\Pages;

use App\Filament\Resources\Congresses\CongressResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCongress extends ViewRecord
{
    protected static string $resource = CongressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
