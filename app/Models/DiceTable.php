<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiceTable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function diceParent()
    {
        return $this->belongsTo(Dice::class, 'parent_id');

    }

    public function diceRows()
    {
        return $this->hasMany(DiceRow::class, 'dice_id');

    }
}
