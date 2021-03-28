<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'shortTerm_id',
        'longTerm_id',  
        'src'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function LongTermDevices()
    {
        return $this->belongsTo(DevicesLongTerm::class);
    }

    public function ShortTermDevices()
    {
        return $this->belongsTo(ShortTermDevices::class);
    }

}
