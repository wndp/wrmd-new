<?php

namespace Tests\Feature\Searching;

use App\Actions\SearchPreciseTaxa;
use App\Models\CommonName;
use App\Models\Taxon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SearchPreciseTaxaTest extends TestCase
{
    // protected $query;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->query = new SearchPreciseTaxa();
    // }

    // use CreateCase;
    // use CreatesTeamUser;
    use RefreshDatabase;

    public $connectionsToTransact = ['singlestore', 'wildalert'];

    public function test_it_precisely_searches_a_common_name(): void
    {
        CommonName::factory()->createQuietly(['common_name' => 'purple finch']);
        CommonName::factory()->createQuietly(['common_name' => 'purple finches']);

        $results = SearchPreciseTaxa::run('purple finch');

        $this->assertCount(1, $results);
        $this->assertEquals('purple finch', $results[0]['common_name']);
    }

    public function test_it_precisely_searches_a_comma_seperated_common_name(): void
    {
        CommonName::factory()->createQuietly(['common_name' => 'cassins finch']);
        CommonName::factory()->createQuietly(['common_name' => 'cassins finches']);

        $results = SearchPreciseTaxa::run('finch, cassins');

        $this->assertCount(1, $results);
        $this->assertEquals('cassins finch', $results[0]['common_name']);
    }

    public function test_it_precisely_searches_a_species_by_alpha_code(): void
    {
        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['alpha_code' => 'fobr'])->id,
            'common_name' => 'foo bar',
        ]);

        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['alpha_code' => 'fobz'])->id,
            'common_name' => 'foo baz',
        ]);

        $results1 = SearchPreciseTaxa::run('fob');
        $this->assertCount(0, $results1);

        $results2 = SearchPreciseTaxa::run('fobr');
        $this->assertCount(1, $results2);
        $this->assertEquals('foo bar', $results2[0]['common_name']);
    }

    public function test_it_precisely_searches_a_common_name_by_alpha_code(): void
    {
        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['alpha_code' => 'fobr'])->id,
            'common_name' => 'foo bar',
        ]);

        CommonName::factory()->createQuietly(['common_name' => 'foo baz', 'subspecies' => 'mer', 'alpha_code' => 'fobz']);

        $results1 = SearchPreciseTaxa::run('fob');
        $this->assertCount(0, $results1);

        $results2 = SearchPreciseTaxa::run('fobz');
        $this->assertCount(1, $results2);
        $this->assertEquals('foo baz', $results2[0]['common_name']);
    }

    public function test_it_precisely_searches_a_spesies_by_sientific_species_genus_name(): void
    {
        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['species' => 'borgali'])->id,
            'common_name' => 'foo bar',
        ]);

        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly([
                'genus' => 'plastersize',
                'species' => 'borgali',
            ]),
            'common_name' => 'foo bar',
        ]);

        $results1 = SearchPreciseTaxa::run('plastersize borgal');
        $this->assertCount(0, $results1);

        $results2 = SearchPreciseTaxa::run('plastersize borgali');
        $this->assertCount(1, $results2);
        $this->assertEquals('foo bar', $results2[0]['common_name']);
    }

    public function test_if_a_common_name_and_an_alpha_code_are_the_same_the_common_name_is_preferred(): void
    {
        $commonName = CommonName::factory()->createQuietly(['common_name' => 'kaka']);

        CommonName::factory()->createQuietly([
            'taxon_id' => Taxon::factory()->createQuietly(['alpha_code' => 'kaka']),
        ]);

        $results = SearchPreciseTaxa::run('kaka');

        $this->assertCount(1, $results);
        $this->assertSame($commonName->taxon_id, $results->first()['taxon_id']);
    }
}
