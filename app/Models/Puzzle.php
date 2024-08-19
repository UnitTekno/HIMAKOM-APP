<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Puzzle extends Model
{
    use HasFactory;
    protected $fillable = [
        'index',
        'title',
        'picture',
        'expected_answer',
        'index'
    ];

    /**
     * The attributes should be casted to native types.
     * 
     * @return Attribute
     */
    protected function picture(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (file_exists(storage_path('app/public/' . config('dirpath.puzzle.pictures') . '/' . $value)) && $value != null) {
                    return asset('storage/' . config('dirpath.puzzle.pictures') . '/' . $value);
                }
            },
        );
    }
}
