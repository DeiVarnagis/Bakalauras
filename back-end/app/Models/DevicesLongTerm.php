<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Nullable;

class DevicesLongTerm extends Model
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
        'src',
    ];

    protected $guarded = [];

    protected $appends = ['type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deviceTransfer()
    {
        return $this->hasMany(DevicesTransfer::class, "longTerm_id");
    }

    public function accessories()
    {
          return $this->hasMany(Accessories::class, "longTerm_id")->get();
    }

    public function DevicesLends()
    {
        return $this->hasOne(DeviceLend::class, "longTerm_id");
    }

    public function getHistory()
    {
        return $this->deviceTransfer()->join('users as receiver', function ($join) {
            $join->on('devices_transfers.user_id', '=', 'receiver.id');
        })
        ->join('users as owner', function ($join) {
            $join->on('devices_transfers.owner_id', '=', 'owner.id');
        })
        ->leftJoin('devices_long_terms', function ($join) {
            $join->on('devices_transfers.longTerm_id', '=', 'devices_long_terms.id');
        })
        ->select('receiver.name as receiver_name',
        'receiver.surname as receiver_surname',
        'owner.name as owner_name',
        'owner.surname as owner_surname',
        'devices_transfers.action',
        'devices_transfers.updated_at',
        'devices_long_terms.created_at'
        )->where('devices_transfers.action', '>=', 1)->where('devices_transfers.state', 1)->orderBy('updated_at', 'desc')->get();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getTypeAttribute()
    {
        return "LongTerm";
    }

    public function validateData()
    {
        return request()->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:devices_long_terms',
            'serialNumber' => 'required|string|max:255|unique:devices_long_terms',
            'amount' => 'required|numeric',
            'src' => 'nullable'
        ]);
    }

    public function validateUpdate($id)
    {
        return request()->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'code' => "required|string|max:255|unique:devices_long_terms,code,$id",
            'serialNumber' => "required|string|max:255|unique:devices_long_terms,serialNumber,$id",
            'amount' => 'required|numeric',
            'src' => 'nullable',
            'deviceType' => 'required|string'
        ]);
    }
}
