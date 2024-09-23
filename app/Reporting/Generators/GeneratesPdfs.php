<?php

namespace App\Reporting\Generators;

use Illuminate\Support\Str;

trait GeneratesPdfs
{
    /**
     * Merge the reports options with default options.
     */
    private function mergeOptionsWithDefaults(): array
    {
        $defaults = [
            'lowquality' => true,
            'viewport-size' => '1000',
            'pageSize' => 'Letter',
            'load-error-handling' => 'ignore',
            'enable-smart-shrinking' => true,
            'encoding' => 'utf-8',
            'dpi' => 96,
        ];

        $options = $this->report->options();

        if (! array_key_exists('no-header', $options)) {
            $defaults = array_merge($defaults, [
                'marginTop' => '13mm',
                'headerSpacing' => '3',
                'headerLeft' => $this->report->title(),
                'headerRight' => 'Page [page] of [toPage]',
                'headerFontSize' => '9',
            ]);
        } else {
            unset($options['no-header']);
        }

        if (! array_key_exists('no-footer', $options)) {
            $defaults = array_merge($defaults, [
                'marginBottom' => '13mm',
                'footerSpacing' => '3',
                'footerLeft' => $this->report->team->name.', '.$this->report->team->locale,
                'footerRight' => Str::random(10),
                'footerFontSize' => '9',
            ]);
        } else {
            unset($options['no-footer']);
        }

        if (array_key_exists('viewportSize', $options)) {
            $defaults = array_merge($defaults, [
                'viewport-size' => $options['viewportSize'],
            ]);
        }

        return array_merge($defaults, $options);
    }
}
