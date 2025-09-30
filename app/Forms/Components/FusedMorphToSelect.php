<?php

namespace App\Forms\Components;

use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class FusedMorphToSelect extends MorphToSelect
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->contained(false);

        $this->hiddenLabel();

        $this->extraAttributes(["class" => "pt-0"]);

        $this->schema(function (MorphToSelect $component): array {
            $relationship = $component->getRelationship();
            $typeColumn = $relationship->getMorphType();
            $keyColumn = $relationship->getForeignKeyName();

            $types = $component->getTypes();
            $isRequired = $component->isRequired();

            $selectedTypeKey = $component->getRawState()[$typeColumn] ?? null;
            $selectedType = $selectedTypeKey ? ($component->getTypes()[$selectedTypeKey] ?? null) : null;

            $typeSelect = Select::make($typeColumn)
                ->label($component->getLabel())
                ->hiddenLabel()
                ->options(array_map(
                    fn (Type $type): string => $type->getLabel(),
                    $types,
                ))
                ->native($component->isNative())
                ->required($isRequired)
                ->live()
                ->afterStateUpdated(function (Set $set) use ($component, $keyColumn): void {
                    $set($keyColumn, null);
                    $component->callAfterStateUpdated();
                });

            $keySelect = Select::make($keyColumn)
                ->label(fn (Get $get): ?string => ($types[$get($typeColumn)] ?? null)?->getLabel())
                ->hiddenLabel()
                ->options(fn (Select $component, Get $get): ?array => $component->evaluate(($types[$get($typeColumn)] ?? null)?->getOptionsUsing))
                ->getSearchResultsUsing(fn (Select $component, Get $get, $search): ?array => $component->evaluate(($types[$get($typeColumn)] ?? null)?->getSearchResultsUsing, ['search' => $search]))
                ->getOptionLabelUsing(fn (Select $component, Get $get, $value): ?string => $component->evaluate(($types[$get($typeColumn)] ?? null)?->getOptionLabelUsing, ['value' => $value]))
                ->native($component->isNative())
                ->required(fn (Get $get): bool => filled(($types[$get($typeColumn)] ?? null)))
                ->hidden(fn (Get $get): bool => blank(($types[$get($typeColumn)] ?? null)))
                ->dehydratedWhenHidden()
                ->searchable($component->isSearchable())
                ->searchDebounce($component->getSearchDebounce())
                ->searchPrompt($component->getSearchPrompt())
                ->searchingMessage($component->getSearchingMessage())
                ->noSearchResultsMessage($component->getNoSearchResultsMessage())
                ->loadingMessage($component->getLoadingMessage())
                ->allowHtml($component->isHtmlAllowed())
                ->optionsLimit($component->getOptionsLimit())
                ->preload($component->isPreloaded())
                ->when(
                    $component->isLive(),
                    fn (Select $component) => $component->live(onBlur: $this->isLiveOnBlur()),
                )
                ->afterStateUpdated(function () use ($component): void {
                    $component->callAfterStateUpdated();
                });

            if ($callback = $component->getModifyTypeSelectUsingCallback()) {
                $typeSelect = $component->evaluate($callback, [
                    'select' => $typeSelect,
                ]) ?? $typeSelect;
            }

            if ($callback = $component->getModifyKeySelectUsingCallback()) {
                $keySelect = $component->evaluate($callback, [
                    'select' => $keySelect,
                ]) ?? $keySelect;
            }

            if ($callback = $selectedType?->getModifyKeySelectUsingCallback()) {
                $keySelect = $component->evaluate($callback, [
                    'select' => $keySelect,
                ]) ?? $keySelect;
            }

            return [
                FusedGroup::make([$typeSelect, $keySelect])->columns(2)->label($this->getLabel()),
            ];
        });
    }
}
