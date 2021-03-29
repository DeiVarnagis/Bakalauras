<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class DevicesShortTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'serialNumber',
        'amount',
        'user_id',
        'state',
        'action',
        'user_id' ,
        'src'
    ];
    protected $appends = ['type'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTypeAttribute()
    {
        return "ShortTerm";
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function deviceTransfer()
    {
        return $this->hasMany(DevicesTransfer::class,"shortTerm_id");
    }

    public function accessories()
    {
        return $this->hasMany(Accessories::class, "shortTerm_id")->get();
    }

    public function DevicesLends()
    {
        return $this->hasOne(DeviceLend::class, "shortTerm_id");
    }

    public function getHistory()
    {
        return $this->deviceTransfer()->join('users as receiver', function ($join) {
            $join->on('devices_transfers.user_id', '=', 'receiver.id');
        })
        ->join('users as owner', function ($join) {
            $join->on('devices_transfers.owner_id', '=', 'owner.id');
        })
        ->leftJoin('devices_short_terms', function ($join) {
            $join->on('devices_transfers.shortTerm_id', '=', 'devices_short_terms.id');
        })
        ->select('receiver.name as receiver_name',
        'receiver.surname as receiver_surname',
        'owner.name as owner_name',
        'owner.surname as owner_surname',
        'devices_transfers.action',
        'devices_transfers.updated_at',
        'devices_short_terms.created_at'
        )->where('devices_transfers.action', '>', 1)->where('devices_transfers.state', 1)->orderBy('updated_at', 'desc')->get();
    }


    public function validateData()
    {
        return request()->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'serialNumber' => 'required|string|max:255|unique:devices_short_terms',
            'amount' => 'required|numeric',
            'src' => 'nullable'
            
        ]);
    }

    public function validateUpdate($id)
    {
        return request()->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'serialNumber' => "required|string|max:255|unique:devices_short_terms,serialNumber,$id",
            'amount' => 'required|numeric',
            'src' => 'nullable',
            'deviceType' => 'required|string',
        ]);
    }
    
}
