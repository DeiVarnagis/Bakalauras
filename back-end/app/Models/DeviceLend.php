<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class DeviceLend extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'user_id',
        'shortTerm_id',
        'longTerm_id',  
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function lendAccessories()
    {
        return $this->hasMany(AccessoriesLend::class, "lend_device_id");
    }

    public function lendedDeviceShort()
    {
        return $this->belongsTo(DevicesShortTerm::class);
    }

    public function lendedDeviceLong()
    {
        return $this->belongsTo(DevicesLongTerm::class);
    }
}
