<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiceRow extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dice()
    {
        return $this->belongsTo(DiceTable::class);
    }

    public function getSameCell()
    {
        if ($this->s1 != null) return 1;
        if ($this->s2 != null) return 2;
        if ($this->s3 != null) return 3;
        if ($this->s4 != null) return 4;
        return null;
    }


}
