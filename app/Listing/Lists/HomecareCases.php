<?php

namespace App\Listing\Lists;

use App\Domain\Admissions\Admission;
use App\Listing\LiveList;
use App\Domain\People\Person;
use Illuminate\Pagination\LengthAwarePaginator;

class HomecareCases extends LiveList
{
    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): LengthAwarePaginator
    {
        $columns = [
            'patient_locations.moved_in_at',
            'patient_locations.area',
            'patient_locations.enclosure',
        ];

        $volunteer = Person::find($this->request->rescuerId);

        return Admission::where('team_id', $this->team->id)
            ->select($columns)
            ->with('patient')
            ->selectAdmissionKeys()
            ->joinTables($columns)
            ->where('patients.disposition', 'Pending')
            ->where('patient_locations.where_holding', 'Homecare')
            ->where('patient_locations.area', 'like', "%{$volunteer->first_name}%")
            ->where('patient_locations.area', 'like', "%{$volunteer->last_name}%")
            ->paginate(15);
    }
}
