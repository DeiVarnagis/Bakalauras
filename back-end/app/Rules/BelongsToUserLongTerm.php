<?php

namespace App\Rules;

use App\Models\DeviceLend;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class BelongsToUserLongTerm implements
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

            if (
                DeviceLend::where('user_id', $this->user->id)
                ->where("longTerm_id", $this->data->longTerm_id)
                ->where('owner_id', $this->data->user_id)->first() !== null
            ) {
                return true;
            }
            $this->errorMessage = 3;
            return false;
        } elseif ($this->state == 2) {
            $device = User::find($this->user->id)->DevicesLongTerm()->find($this->data->longTerm_id);
            if (
                !empty(DeviceLend::where('user_id', $this->user->id)->where('longTerm_id', $this->data->longTerm_id)->first())
                || ($device !== null && $device->state == 0) || ($this->user->admin)
                ) 
            {
                return true;
            }
            $this->errorMessage = 1;
            return false;
        } else {
            $device = User::find($this->user->id)->DevicesLongTerm()->find($this->data->longTerm_id);
            if($this->user->admin)
            {
                return true;
            }
            if (($device !== null && !isset($this->data->shortTerm_id))) {
                if ($device->state == 0 || $device->state == -1 )  {
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
        if (isset($this->data->shortTerm_id)) {
            return 'Tik vienas iš laukų shortTerm_id arba longTerm_id yra būtinas';
        } elseif ($this->errorMessage == 1) {
            return 'Prietaisas jau laukia patvirtinimo';
        } elseif ($this->errorMessage == 2) {
            return 'Prietaisas paskolintas kitam vartotojui';
        } elseif ($this->errorMessage == 3) {
            return 'Veiksmas negalimas';
        }
        return 'Vartotojui nepriklauso šis prietaisas';
    }
}
