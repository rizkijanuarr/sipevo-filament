<?php

namespace App\Enums;

// use Filament\Support\Contracts\HasLabel;

// enum PengaduanStatus: string implements HasLabel
// {
//     case PENDING = 'pending';
//     case COMPLETED = 'completed';
//     case CANCELLED = 'cancelled';

//     public function getLabel(): ?string
//     {
//         return str($this->value)->title();
//     }

//     public function getColor(): string
//     {
//         return match ($this) {
//             self::PENDING => 'warning',
//             self::COMPLETED => 'success',
//             self::CANCELLED => 'danger',
//         };
//     }
// }

enum PengaduanStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return str($this->value)->title();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
