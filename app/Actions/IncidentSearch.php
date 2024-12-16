<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Models\Incident;
use App\Models\Team;
use App\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class IncidentSearch extends Search
{
    use AsAction;

    public function handle(Team $team, Request $request)
    {
        $this->arguments = $this->sanitizeRequest($request);

        return Incident::select('incidents.*')
            ->when($this->shouldJoinPerson(), function ($query) {
                $query->join('people', 'incidents.responder_id', '=', 'people.id');
            })
            ->when($this->shouldJoinCommunications(), function ($query) {
                $query->join('communications', 'incidents.id', '=', 'communications.incident_id');
            })
            ->where('incidents.team_id', $team->id)
            //->dateRange($request->input('admitted_at_from'), $request->input('admitted_at_to'))
            ->where(fn ($query) => $this->whereSearch($query))
            ->latest('incidents.created_at')
            ->get();

        //->orderBy('admissions.case_year')
        //->orderBy('admissions.case_id')
        //->queryCache()
        //->get();

        //$this->joinResponderIfNeeded();
        //$this->joinCommunicationsIfNeeded();

        // $this->arguments->each(function ($value, $key) {
        //     $this->renderSearchArgument($key, $value);
        // });

        //return $query->latest('incidents.created_at')->get();
    }

    public function sanitizeRequest(Request $request): Collection
    {
        return Collection::make(
            $request->except([
                '_token',
                '_method',
            ])
        )
            ->filter();
        //->mapWithKeys(fn ($value, $key) => [$this->sanitizeKey($key) => $value]);
    }

    /**
     * Determine if people are being searched for.
     */
    private function shouldJoinPerson(): bool
    {
        return $this->arguments->contains(function ($value, $key) {
            return Str::contains($key, ['first_name']);
        });
    }

    /**
     * Determine if communications are being searched for.
     */
    private function shouldJoinCommunications(): bool
    {
        return $this->arguments->contains(function ($value, $key) {
            return Str::contains($key, ['communication_at', 'communication_by', 'communication']);
        });
    }
}
