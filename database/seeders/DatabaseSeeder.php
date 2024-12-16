<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->cleanMedia();
        $this->cleanDatabase();

        $this->call([
            AttributeOptionSeeder::class,
            PermissionsSeeder::class,
        ]);

        Cache::clear();
    }

    private function cleanMedia()
    {
        if (App::isProduction()) {
            return;
        }

        Media::get()->each->delete();
    }

    private function cleanDatabase()
    {
        if (App::isProduction()) {
            return;
        }

        DB::table('information_schema.tables')
            ->where('table_schema', app('db')->getDatabaseName())
            ->whereNotIn('table_name', [
                'migrations',
            ])
            ->pluck('TABLE_NAME')
            ->each(fn ($table) => DB::table($table)->truncate());
    }
}
