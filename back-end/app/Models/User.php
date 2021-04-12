<?php

namespace App\Models;

use App\Notifications\PasswordReset;
use App\Notifications\VerifyApiEmail;
use DateTimeInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements
    MustVerifyEmail,
    JWTSubject


{
    use HasFactory,  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phoneNumber',
        'address',
        'admin',
        'birth',
        'src'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "surname" => $this->surname,
            "email" => $this->email,
            "admin" => $this->admin
        ];
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function sendApiEmailVerificationNotification()
    {
        $this->notify(new VerifyApiEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    public function DevicesLongTerm()
    {
        return $this->HasMany(DevicesLongTerm::class);
    }

    public function DevicesShortTerm()
    {
        return $this->HasMany(DevicesShortTerm::class);
    }

    public function DeviceTransfer()
    {
        return $this->HasMany(DevicesTransfer::class);
    }

    public function DevicesLends()
    {
        return $this->hasMany(DeviceLend::class);
    }

    public function leavingWorkMessages()
    {
        return $this->hasMany(LeavingWork::class);
    }

    public function getBorrowedDevices()
    {
        $collection = collect();
        foreach ($this->DevicesLends()->select("shortTerm_id", "longTerm_id")->get() as $devicesID) {

            if ($devicesID->shortTerm_id != null) {
                $collection->add(DevicesShortTerm::where('id', $devicesID->shortTerm_id)->first());
            } elseif ($devicesID->longTerm_id != null) {
                $collection->add(DevicesLongTerm::where('id', $devicesID->longTerm_id)->first());
            }
        }
        return $collection;
    }

    public function confirmDeiviceTransfer($userTo, $deviceID, $deviceType)
    {
        if ($deviceType == "ShortTerm") {
            $device = $this->DevicesShortTerm->find($deviceID);
            $device->user_id = $userTo;
            $device->save();
        } else {
            $device = $this->DevicesLongTerm->find($deviceID);
            $device->user_id = $userTo;
            $device->save();
        }
    }


    public function allDevices($state, $search)
    {
        if ($state == "all") {
            $longTerm = DevicesLongTerm::where('name', 'like', "%" . $search . "%")->where('user_id', '!=', auth()->user()->id)->with('user')->get();
            $shortTerm = DevicesShortTerm::where('name', 'like', "%" . $search . "%")->where('user_id', '!=', auth()->user()->id)->with('user')->get();
            $merged = $longTerm->concat($shortTerm);
            return $merged;
        }

        $longTerm =  DevicesLongTerm::where('state', $state)->where('name', 'like', "%" . $search . "%")->where('user_id', '!=', auth()->user()->id)->with('user')->get();
        $shortTerm =  DevicesShortTerm::where('state', $state)->where('name', 'like', "%" . $search . "%")->where('user_id', '!=', auth()->user()->id)->with('user')->get();
        $merged = $longTerm->concat($shortTerm);
        return $merged;
    }

    public function userDevices($state, $search)
    {
        if ($state == "all") {
            $longTerm = $this->DevicesLongTerm()->where('name', 'like', "%" . $search . "%")->get();
            $shortTerm = $this->DevicesShortTerm()->where('name', 'like', "%" . $search . "%")->get();
            $merged = $longTerm->concat($shortTerm);
            return $merged;
        } elseif ($state == "Borrowed") {
            return $this->getBorrowedDevices($state, $search);
        }

        $longTerm =  $this->DevicesLongTerm()->where('state', $state)->where('name', 'like', "%" . $search . "%")->get();
        $shortTerm =  $this->DevicesShortTerm()->where('state', $state)->where('name', 'like', "%" . $search . "%")->get();
        $merged = $longTerm->concat($shortTerm);
        return $merged;
    }

    public function getFilteredShort($state, $search)
    {
        if ($state == "all") {
            return $this->DevicesShortTerm()->where('name', 'like', "%" . $search . "%")->get();
        }
        return $this->DevicesShortTerm()->where([
            ['state', $state],
            ['name', 'like', "%" . $search . "%"]
        ])->get();
    }

    public function getFilteredLong($state, $search)
    {
        if ($state == "all") {
            return $this->DevicesLongTerm()->where('name', 'like', "%" . $search . "%")->get();
        }
        return $this->DevicesLongTerm()->where([
            ['state', $state],
            ['name', 'like', "%" . $search . "%"]
        ])->get();
    }

    public function messagesCount()
    {
        return  DevicesTransfer::where('user_id', $this->id)->where('state', '0')->count() + $this->leavingWorkMessages()->where('state', '0')->count();
    }

    public function messages($filter, $search)
    {
        $messages = $this->DeviceTransfer()
            ->join('users', function ($join) {
                $join->on('devices_transfers.owner_id', '=', 'users.id');
            })
            ->leftjoin('devices_long_terms', function ($join) {
                $join->on('devices_transfers.longTerm_id', '=', 'devices_long_terms.id');
            })
            ->leftjoin('devices_short_terms', function ($join) {
                $join->on('devices_transfers.shortTerm_id', '=', 'devices_short_terms.id');
            })
            ->select(
                'devices_transfers.id as id',
                'users.name as owner_name',
                'users.surname as owner_surname',
                'devices_transfers.action as action',
                'devices_long_terms.name as device_name_long',
                'devices_long_terms.code as device_code_long',
                'devices_short_terms.name as device_name_short',
                'devices_short_terms.code as device_code_short',
                'devices_transfers.state as device_state'
            )
            ->where('devices_transfers.user_id', auth()->user()->id)->where('devices_transfers.state', $filter)->where('users.name', 'like', "%" . $search . "%")->get();

        $leavingWork = $this->leavingWorkMessages()->join('users', function ($join) {
            $join->on('leaving_works.owner_id', '=', 'users.id');
        })->select(
            'leaving_works.id as id',
            'users.name as owner_name',
            'users.surname as owner_surname',
            'leaving_works.state as device_state'
        )->where('leaving_works.state', $filter)->get();
        return $messages->concat($leavingWork);
    }
}
