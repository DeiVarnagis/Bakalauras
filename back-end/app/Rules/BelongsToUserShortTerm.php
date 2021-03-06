<?php

namespace App\Rules;

use App\Models\User;
use App\Models\DeviceLend;
use Illuminate\Contracts\Validation\Rule;

class BelongsToUserShortTerm implements
    Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $user;
    protected $data;
    protected $state;
    protected $errorMessage;

    public function __construct($data)
    {
        $this->data = $data;
        $this->user = auth()->user();
        $this->state = $data->action;
        $this->errorMessage = 0;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->state == 3) {
            $check = DeviceLend::where('user_id', $this->user->id)
                ->where("shortTerm_id", $this->data->shortTerm_id)
                ->where('owner_id', $this->data->user_id)->first();

            if ($check !== null) {
                return true;
            }
            $this->errorMessage = 3;
            return false;
        } elseif ($this->state == 2) {
            $device = User::find($this->user->id)->DevicesShortTerm()->find($this->data->shortTerm_id);
            if (
                !empty(DeviceLend::where('user_id', $this->user)->where('shortTerm_id', $this->data->shortTerm_id)->first())
                || ($device !== null && $device->state == 0) || ($this->user->admin)
            ) {
                return true;
            }
            return false;
        } else {
            $device = User::find($this->user->id)->DevicesShortTerm()->find($this->data->shortTerm_id);
            if($this->user->admin)
            {
                return true;
            }
            if (($device !== null && !isset($this->data->longTerm_id))) {
                if ($device->state == 0 || $device->state == -1) {
                    return true;
                }
                $this->errorMessage = $device->state;
                return false;
            }
            $this->errorMessage = 0;
            return false;
        }
    }

    public function message()
    {
        if (isset($this->data->longTerm_id)) {
            return 'Tik vienas i?? lauk?? shortTerm_id arba longTerm_id yra b??tinas';
        } elseif ($this->errorMessage == 1) {
            return 'Prietaisas jau laukia patvirtinimo';
        } elseif ($this->errorMessage == 2) {
            return 'Prietaisas paskolintas kitam vartotojui';
        } elseif ($this->errorMessage == 3) {
            return 'Prietaisas pasiskolintas';
        }
        return 'Vartotojui nepriklauso ??is prietaisas';
    }
}
