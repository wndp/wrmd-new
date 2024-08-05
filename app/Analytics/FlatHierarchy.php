<?php

namespace App\Analytics;

use Illuminate\Support\Collection;

class FlatHierarchy
{
    protected $data;

    protected $hierarchy;

    public function __construct($name, $data)
    {
        $this->data = $data;
        $this->id = 1;
        $this->hierarchy = new Collection([[
            'id' => '0.0',
            'parent' => '',
            'name' => $name,
        ]]);
    }

    public function handle()
    {
        foreach ($this->data as $key => $value) {
            $this->hierarchy->push([
                'id' => "1.$this->id",
                'parent' => '0.0',
                'name' => $key,
            ]);

            if (! empty($value)) {
                $this->handleChildNode($value, "1.$this->id", 2);
            }

            $this->id++;
        }

        return $this->hierarchy;
    }

    public function handleChildNode($array, $parentId, $level)
    {
        $n = 0;

        foreach ($array as $key => $value) {
            $name = is_string($value) ? $value : $key;

            $this->hierarchy->push([
                'id' => "$level.$this->id",
                'parent' => $parentId,
                'name' => $name,
            ]);

            if (is_array($value)) {
                $this->handleChildNode($value, "$level.$this->id", $level + 1);
            }

            $n++;

            if ($n !== count($array)) {
                $this->id++;
            }
        }
    }
}
