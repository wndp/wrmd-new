<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use Illuminate\Support\Facades\DB;

class MaintenanceToDo extends Number
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->now = number_format($this->query());
    }

    public function query()
    {
        return DB::table($this->filters->table)->where(json_decode($this->filters->where, true))->count();
    }
}
