<?php

namespace App\Reporting;

use App\Domain\Accounts\Account;
use App\Reporting\Reports\Annual\Bz;
use App\Reporting\Reports\Annual\BzQuarterly;
use App\Reporting\Reports\Annual\Ca;
use App\Reporting\Reports\Annual\CaNb;
use App\Reporting\Reports\Annual\Nz;
use App\Reporting\Reports\Annual\NzOld;
use App\Reporting\Reports\Annual\Us;
use App\Reporting\Reports\Annual\UsAl;
use App\Reporting\Reports\Annual\UsAr;
use App\Reporting\Reports\Annual\UsAz;
use App\Reporting\Reports\Annual\UsCa;
use App\Reporting\Reports\Annual\UsCo;
use App\Reporting\Reports\Annual\UsGa;
use App\Reporting\Reports\Annual\UsIl;
use App\Reporting\Reports\Annual\UsIn;
use App\Reporting\Reports\Annual\UsKy;
use App\Reporting\Reports\Annual\UsLa;
use App\Reporting\Reports\Annual\UsMa;
use App\Reporting\Reports\Annual\UsMaEntities;
use App\Reporting\Reports\Annual\UsMd;
use App\Reporting\Reports\Annual\UsMdRabies;
use App\Reporting\Reports\Annual\UsMi;
use App\Reporting\Reports\Annual\UsMn;
use App\Reporting\Reports\Annual\UsMnSummary;
use App\Reporting\Reports\Annual\UsMo;
use App\Reporting\Reports\Annual\UsMs;
use App\Reporting\Reports\Annual\UsNj;
use App\Reporting\Reports\Annual\UsNm;
use App\Reporting\Reports\Annual\UsNyLog;
use App\Reporting\Reports\Annual\UsNyRvs;
use App\Reporting\Reports\Annual\UsNyTally;
use App\Reporting\Reports\Annual\UsOr;
use App\Reporting\Reports\Annual\UsPa;
use App\Reporting\Reports\Annual\UsPaHerps;
use App\Reporting\Reports\Annual\UsRi;
use App\Reporting\Reports\Annual\UsTn;
use App\Reporting\Reports\Annual\UsTx;
use App\Reporting\Reports\Annual\UsTxQuarterly;
use App\Reporting\Reports\Annual\UsVa;
use App\Reporting\Reports\Annual\UsWa;
use App\Reporting\Reports\Annual\UsWaLedger;
use App\Reporting\Reports\Annual\UsWi;
use App\Reporting\Reports\Annual\UsWiQuarterly;
use App\Reporting\Reports\AssetReport;
use Illuminate\Support\Collection;

class AnnualReports
{
    /**
     * Get a collection of the locales with an annual report.
     */
    public static function locales(): Collection
    {
        return new Collection([
            'BZ', 'CA', 'CA-NB', 'NZ', 'US', 'US-AL', 'US-AZ', 'US-CA',
            'US-CO', 'US-GA', 'US-IL', 'US-IN', 'US-KY',
            'US-LA', 'US-MD', 'US-MI', 'US-MO', 'US-MS',
            'US-NM', 'US-NY', 'US-OR', 'US-PA', 'US-TN',
            'US-TX', 'US-VA', 'US-WA', 'US-WI',
        ]);
    }

    /**
     * Get the annual reports for the requested locale.
     */
    public static function byLocale(Account $account, string $subdivision = null): array
    {
        switch (static::iso3166($account->country, $subdivision)) {
            case 'BZ':
                return [
                    new Bz($account),
                    new BzQuarterly($account),
                ];

            case 'CA':
                return [
                    new Ca($account),
                    new AssetReport('pdfs/canada_atlantic_region.pdf', 'Canada Atlantic Region Rehabilitation Report Form (official)'),
                ];

            case 'CA-NB':
                return [
                    new CaNb($account),
                ];
                //New Brunswick

            case 'NZ':
                return [
                    new Nz($account),
                    new NzOld($account),
                    new AssetReport('pdfs/new_zealand.pdf', "New Zealand Rehabilitator's Annual Report (official)"),
                ];

            case 'US':
                return [
                    new Us($account),
                    new AssetReport('pdfs/USFWS-3-202-4.pdf', 'USFWS Form 3-202-4 (official)'),
                ];

            case 'US-AL':
                return [
                    new UsAl($account),
                    new AssetReport('pdfs/alabama.pdf', 'Alabama (official)'),
                ];

            case 'US-AR':
                return [
                    new UsAr($account),
                    new AssetReport('pdfs/arkansas.pdf', 'Arkansas (official)'),
                ];

            case 'US-AZ':
                return [
                    new UsAz($account),
                    new AssetReport('pdfs/arizona.pdf', 'Arizona (official)'),
                ];

            case 'US-CA':
                return [
                    new UsCa($account),
                    new AssetReport('pdfs/california.pdf', 'California (official)'),
                ];

            case 'US-CO':
                return [
                    new UsCo($account),
                    new AssetReport('pdfs/colorado.pdf', 'Colorado (official)'),
                ];

            case 'US-DC':
                return [
                    new UsMd($account),
                    new UsMdRabies($account),
                    new AssetReport('pdfs/maryland.pdf', 'Maryland (official)'),
                    new AssetReport('pdfs/maryland-rabies-vector-species.pdf', 'Maryland Rabies Vector Species (official)'),
                ];

            case 'US-IL':
                return [
                    new UsIl($account),
                    new AssetReport('pdfs/illinois.pdf', 'Illinois (official)'),
                ];

            case 'US-IN':
                return [
                    new UsIn($account),
                    new AssetReport('pdfs/indiana.pdf', 'Indiana (official)'),
                ];

            case 'US-GA':
                return [
                    new UsGa($account),
                    new AssetReport('pdfs/georgia.pdf', 'Georgia (official)'),
                ];

            case 'US-KY':
                return [
                    new UsKy($account),
                    new AssetReport('pdfs/kentucky.pdf', 'Kentucky (official)'),
                ];

            case 'US-LA':
                return [
                    new UsLa($account),
                    new AssetReport('pdfs/louisiana.pdf', 'Louisiana (official)'),
                ];

            case 'US-MA':
                return [
                    new UsMa($account),
                    new UsMaEntities($account),
                    new AssetReport('pdfs/massachusetts.xlsx', 'Massachusetts (official)'),
                ];

            case 'US-MD':
                return [
                    new UsMd($account),
                    new UsMdRabies($account),
                    new AssetReport('pdfs/maryland.pdf', 'Maryland (official)'),
                    new AssetReport('pdfs/maryland-rabies-vector-species.pdf', 'Maryland Rabies Vector Species (official)'),
                ];

            case 'US-MI':
                return [
                    new UsMi($account),
                    new AssetReport('pdfs/michigan.pdf', 'Michigan (official)'),
                ];

            case 'US-MN':
                return [
                    new UsMn($account),
                    new UsMnSummary($account),
                    new AssetReport('pdfs/minnesota.xlsx', 'Minnesota (official)'),
                ];

            case 'US-MO':
                return [
                    new UsMo($account),
                    new AssetReport('pdfs/missouri.pdf', 'Missouri (official)'),
                ];

            case 'US-MS':
                return [
                    new UsMs($account),
                    new AssetReport('pdfs/mississippi.pdf', 'Mississippi (official)'),
                ];

            case 'US-NJ':
                return [
                    new UsNj($account),
                    new AssetReport('pdfs/newjersey.pdf', 'New Jersey (official)'),
                ];

            case 'US-NM':
                return [
                    new UsNm($account),
                    new AssetReport('pdfs/newmexico.pdf', 'New Mexico (official)'),
                ];

            case 'US-NY':
                return [
                    new UsNyTally($account),
                    new UsNyLog($account),
                    new UsNyRvs($account),
                    new AssetReport('pdfs/newyorktally.pdf', 'New York Log Tally (official)'),
                    new AssetReport('pdfs/newyorklog.pdf', 'New York Log (official)'),
                    new AssetReport('pdfs/new_york_rabies_vector_species.pdf', 'New York Rabies Vector Species Log and Tally (official)'),
                ];

            case 'US-OR':
                return [
                    new UsOr($account),
                    new AssetReport('pdfs/oregon.pdf', 'Oregon (official)'),
                ];

            case 'US-PA':
                return [
                    new UsPa($account),
                    new UsPaHerps($account),
                    new AssetReport('pdfs/pennsylvania.pdf', 'Pennsylvania (official)'),
                    new AssetReport('pdfs/pennsylvania_herps.pdf', 'Pennsylvania Amphibian And Reptile Annual Report (official)'),
                ];

            case 'US-RI':
                return [
                    new UsRi($account),
                    new AssetReport('pdfs/rhodeisland.xlsx', 'Rhode Island (official)'),
                ];

            case 'US-VA':
                return [
                    new UsVa($account),
                    new AssetReport('pdfs/virginia.pdf', 'Virginia (official)'),
                ];

            case 'US-TN':
                return [
                    new UsTn($account),
                    new AssetReport('pdfs/tennessee.pdf', 'Tennessee (official)'),
                ];

            case 'US-TX':
                return [
                    new UsTx($account),
                    new UsTxQuarterly($account),
                    new AssetReport('pdfs/texas.pdf', 'Texas Wildlife Rehabilitation Annual Report (official)'),
                    new AssetReport('pdfs/texas-quarterly.xlsx', 'Texas Wildlife Rehabilitation Quarterly Report (official)'),
                ];

            case 'US-WA':
                return [
                    new UsWa($account),
                    new UsWaLedger($account),
                    new AssetReport('pdfs/washington.pdf', 'Washington (official)'),
                    new AssetReport('pdfs/washington2.pdf', 'Washington Ledger (official)'),
                ];

            case 'US-WI':
                return [
                    new UsWi($account),
                    new UsWiQuarterly($account),
                    new AssetReport('pdfs/wisconsin_annual.pdf', 'Wisconsin Annual (official)'),
                    new AssetReport('pdfs/wisconsin_quarterly.pdf', 'Wisconsin Quarterly (official)'),
                ];

            default:
                return [];
        }
    }

    /**
     * Convert a string to ISO-3166 case.
     * https://en.wikipedia.org/wiki/ISO_3166-1
     * https://en.wikipedia.org/wiki/ISO_3166-2.
     */
    private static function iso3166(string $country, string $subdivision = null): string
    {
        return strtoupper(
            implode('-', array_filter(
                [$country, $subdivision]
            ))
        );
    }
}
