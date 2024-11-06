<?php

namespace App\Rules;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class checkIfHasAppointment implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $timeslot = null;
    private $date = null;
    public function __construct($date)
    {
        $this->date = $date;
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
        $appointments = Appointment::where([
            ['appointment_time', '>', $this->date.' 00:00:00'],
            ['appointment_time', '<', $this->date.' 23:59:59']])->get();
        if($appointments && $appointments->count() > 0) {
            foreach ($appointments as $appointment) {
                $hr = Carbon::parse($appointment->appointment_time)->format('H:i');
                if(in_array($hr, $value)){
                    $this->timeslot = $hr;
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->timeslot.' has been booked for the given date';
    }
}
