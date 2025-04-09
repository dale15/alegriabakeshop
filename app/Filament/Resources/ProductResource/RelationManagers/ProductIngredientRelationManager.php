<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Ingredient;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductIngredientRelationManager extends RelationManager
{
    protected static string $relationship = 'productIngredients';
    protected static ?string $title = 'Ingredients';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ingredient_id')
                    ->relationship('ingredient', 'name')
                    ->required()
                    ->native(false)
                    ->reactive()
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $ingredient = Ingredient::find($state);
                        if ($ingredient) {
                            $set('price', $ingredient->price);
                            $set('unit', $ingredient->unit_of_measure);

                            $quantity = $get('quantity');

                            if ($quantity && $quantity > 0) {
                                $baseUnit = $ingredient->getBaseUnit();
                                $set('cost_per_unit', ($ingredient->price / $baseUnit) * $quantity);
                            }
                        } else {
                            $set('price', null);
                            $set('cost_per_unit', null);
                            $set('unit', null);
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->label('Required Quantity')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.1)
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $ingredientId = $get('ingredient_id');
                        $quantity = $get('quantity');

                        if ($ingredientId && $quantity && $quantity > 0) {
                            $ingredient = Ingredient::find($ingredientId);

                            if ($ingredient) {
                                $price = $ingredient->price;
                                $costPerUnit = $price / $ingredient->quantity_in_stock;
                                $cost = $costPerUnit * $quantity;
                                $set('cost_per_unit', $cost);
                            }
                        }
                    }),
                Section::make('Costing')
                    ->schema([
                        TextInput::make('price')->label('Price (per base unit)')->readOnly(),
                        TextInput::make('cost_per_unit')->label('Computed Cost')->readOnly()
                    ])->columns(2)
            ]);
    }

    protected function afterSave(): void
    {
        logger("After Save relation manager go here");
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ingredient.name')
            ->columns([
                Tables\Columns\TextColumn::make('ingredient.name'),
                Tables\Columns\TextColumn::make('quantity')->label('Qty Used'),
                TextColumn::make('computed_cost')
                    ->label('Computed Cost')
                    ->getStateUsing(function ($record) {
                        $quantity = $record->quantity;
                        $costPerUnit = $record->cost_per_unit ?? 0;
                        $unit = $record->ingredient->unit_of_measure ?? '';
                        // $total = $quantity * $costPerUnit;
                        return '₱ ' . number_format($costPerUnit, 2) . " ({$unit})";
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Ingredient')
                    ->modalHeading('Add Ingredient'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(heading: 'Edit Ingredient'),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Delete Ingredient'),
            ])
            ->emptyStateHeading('No Ingredients')
            ->emptyStateDescription('Add your product\'s Ingredient here.')
            ->emptyStateActions(
                [
                    Tables\Actions\CreateAction::make()
                        ->icon('heroicon-m-plus')
                        ->modalHeading('Add Ingredient')
                        ->label('Add Ingredient'),
                ],
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
