<?php

namespace App\Rules;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\Rule;

class appointmentExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void

     */
    private $date;
    public function __construct($date, $id)
    {
        $this->date = $date;
        $this->id = $id;
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
        $appointment_time = $this->date. ' '.$value.':00';
        $appointments = Appointment::where('appointment_time', $appointment_time)
            ->where('id', '!=', $this->id)
            ->count();

        return $appointments <= 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The date and time is taken. Please try another';
    }
}
