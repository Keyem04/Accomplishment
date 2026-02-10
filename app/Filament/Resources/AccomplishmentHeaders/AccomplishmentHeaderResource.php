<?php

namespace App\Filament\Resources\AccomplishmentHeaders;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\AccomplishmentHeader;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AccomplishmentHeaders\Pages\EditAccomplishmentHeader;
use App\Filament\Resources\AccomplishmentHeaders\Pages\ListAccomplishmentHeaders;
use App\Filament\Resources\AccomplishmentHeaders\Pages\CreateAccomplishmentHeader;
use App\Filament\Resources\AccomplishmentHeaders\Schemas\AccomplishmentHeaderForm;
use App\Filament\Resources\AccomplishmentHeaders\Tables\AccomplishmentHeadersTable;
use App\Filament\Resources\AccomplishmentHeaders\RelationManagers\AccomplishmentDetailsRelationManager;

class AccomplishmentHeaderResource extends Resource
{
    protected static ?string $model = AccomplishmentHeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $recordTitleAttribute = 'id';
    protected static string $relationship = 'accomplishmentdetails';

    protected static ?string $modelLabel = 'Accomplishment';
    protected static ?string $pluralModelLabel = 'Accomplishments';

    protected static ?string $navigationLabel = 'Accomplishments';
    protected static ?string $breadcrumb = 'Accomplishments';

    public static function form(Schema $schema): Schema
    {
        return AccomplishmentHeaderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccomplishmentHeadersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AccomplishmentDetailsRelationManager::class,   
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccomplishmentHeaders::route('/'),
            'create' => CreateAccomplishmentHeader::route('/create'),
            'edit' => EditAccomplishmentHeader::route('/{record}/edit'),
        ];
    }
    
    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('department_code', auth()->user()->department_code);
    }
}
