<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UsersResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UsersResource\RelationManagers;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Pengaturan Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->placeholder('Masukan nama...'),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->placeholder('Masukan email...'),
                        TextInput::make('password')
                            ->label('Password')
                            ->required()
                            ->password()
                            ->placeholder('Masukan password...')
                            ->columnSpanFull(),
                    ])->columnSpan(2),
                 ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->icon('heroicon-o-envelope')->color('yellow')->sortable(),
                TextColumn::make('created_at')->label('Dibuat')->formatStateUsing(fn ($state) =>\Carbon\Carbon::parse($state)->format('M d, Y H:i:s'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Diubah')->formatStateUsing(fn ($state) =>\Carbon\Carbon::parse($state)->format('M d, Y H:i:s'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('Filter Dibuat')
                    ->form([
                        DatePicker::make('created_from')->label('Dari'),
                        DatePicker::make('created_until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'] ?? null, fn ($q, $date) =>
                                $q->whereDate('created_at', '>=', $date)
                            )
                            ->when($data['created_until'] ?? null, fn ($q, $date) =>
                                $q->whereDate('created_at', '<=', $date)
                            );
                    }),
            
                Filter::make('updated_at')
                    ->label('Filter Diubah')
                    ->form([
                        DatePicker::make('updated_from')->label('Dari'),
                        DatePicker::make('updated_until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['updated_from'] ?? null, fn ($q, $date) =>
                                $q->whereDate('updated_at', '>=', $date)
                            )
                            ->when($data['updated_until'] ?? null, fn ($q, $date) =>
                                $q->whereDate('updated_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}
