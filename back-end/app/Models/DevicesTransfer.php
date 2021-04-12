<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class DevicesTransfer extends Model
{
    use HasFactory;
    protected $directory = "App\Models\Devices";

    protected $fillable = [
        'owner_id',
        'user_id',
        'shortTerm_id',
        'longTerm_id',  
        'if_transferred',
        'state',
        'action'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function lendAccessories()
    {
        return $this->hasMany(AccessoriesLend::class, "transfer_id");
    }

    public function DevicesLongTerm()
    {
        return $this->belongsTo(DevicesLongTerm::class, 'longTerm_id', 'id');
    }

    public function DevicesShortTerm()
    {
        return $this->belongsTo(DevicesShortTerm::class, 'shortTerm_id', 'id');
    }


    public function returnDeviceType()
    {
        if($this->shortTerm_id !== null)
        {
            return 
            [
                "type" => "ShortTerm",
                "id" => $this->shortTerm_id
            ];
        }
        else
        {
            return 
            [
                "type" => "LongTerm",
                "id" => $this->longTerm_id
            ];
        }
    }


    public function updateDevices($action, $state, $transState)
    {
        $values = $this->returnDeviceType();
        $model = $this->directory . $values['type'];
        $instance = new $model;
        $device = $instance::find($values['id']);
        $device->action = $action;
        if($device->state != 2 || $state == 0)
        {
            $device->state = $state;
        }
        $device->updated_at=Carbon::now();
        $device->save();
        $this->state = $transState;
        $this->save();
    }


    
}
