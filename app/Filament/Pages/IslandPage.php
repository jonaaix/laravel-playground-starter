<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

abstract class IslandPage extends Page
{
    protected string $view = 'filament.island-page';

    abstract protected function islandName(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function islandProps(): array;

    public function getHeading(): string|Htmlable|null
    {
        return '';
    }

    /**
     * @return array{islandName: string, islandProps: array<string, mixed>}
     */
    protected function getViewData(): array
    {
        return [
            'islandName' => $this->islandName(),
            'islandProps' => $this->islandProps(),
        ];
    }
}
