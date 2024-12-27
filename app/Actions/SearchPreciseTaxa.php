<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Models\CommonName;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchPreciseTaxa
{
    use AsAction;

    public function handle(string $search): Collection
    {
        $search = trim($search);

        $results = CommonName::select([
            'common_names.id as id',
            'common_names.taxon_id as taxon_id',
            'common_names.common_name',
            'common_names.language',
        ])
            ->join('taxa', 'common_names.taxon_id', '=', 'taxa.id')
            ->where('common_names.common_name', $this->deCommaIfy($search))

            // A multi-word $search might be a binomial scientific name.
            ->when(
                Str::contains($search, ' '),
                fn ($query) => $query->orWhere(function ($query) use ($search) {
                    [$genus, $species] = array_pad(explode(' ', $search), 2, null);

                    $query->where('genus', $genus)->where('species', $species);
                })
            )

            // If the search contains only four letters it might be an AOU Alpha code.
            ->when(
                Str::length($search) === 4,
                fn ($query) => $query->orWhere('taxa.alpha_code', $search)
                    ->orWhere(function ($query) use ($search) {
                        $query->whereNotNull('subspecies')
                            ->where('common_names.alpha_code', $search);
                    })
            )
            ->get();

        if ($results->unique('taxon_id')->count() === 1) {
            return $this->formatResults($results);
        }

        if (Str::length($search) === 4) {
            $results = $results->where('common_name', $search)->values();

            if ($results->unique('taxon_id')->count() === 1) {
                return $this->formatResults($results);
            }
        }

        return new Collection;
    }

    /**
     * Reorder words if a comma is found in the common name.
     */
    private function deCommaIfy(string $commonName): string
    {
        if (Str::contains($commonName, ',')) {
            $words = preg_split('/[,\s]+/', $commonName);

            return trim($words[1]).' '.trim($words[0]);
        }

        return $commonName;
    }

    /**
     * Format the search results to a consistent format.
     */
    private function formatResults(Collection $results): Collection
    {
        return $results->map(fn ($commonName) => [
            'taxon_id' => $commonName->taxon_id,
            'common_name' => $commonName->common_name,
            'language' => $commonName->language,
        ]);
    }
}
