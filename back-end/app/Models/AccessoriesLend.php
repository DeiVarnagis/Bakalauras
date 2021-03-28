<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class AccessoriesLend extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'amount',
        'accessory_id',
        "transfer_id",
        "lend_device_id",
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function LendDevice()
    {
        return $this->belongsTo(DeviceLend::class);
    }
}
