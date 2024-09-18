<?php

namespace App\Reporting\Generators;

use App\Reporting\Contracts\Generator;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf as MpdfBase;
use Mpdf\Output\Destination;

class Mpdf extends Generator
{
    use GeneratesPdfs;

    /**
     * The Mpdf library.
     *
     * @var Mpdf
     */
    private $mpdf;

    /**
     * Default parameters for Mpdf.
     *
     * @var array
     */
    private $defaultParameters = [
        'mode' => 'c',
        'format' => 'letter',
        'default_font_size' => 0,
        'default_font' => '',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 25,
        'margin_bottom' => 20,
        'margin_header' => 9,
        'margin_footer' => 9,
        'orientation' => 'P',
    ];

    /**
     * Generate the report.
     */
    public function handle(): void
    {
        $this->loadMpdf($this->mergeOptionsWithDefaults());

        $options = $this->report->options();

        $htmls = [];
        foreach ($options['templateContent'] as $key => $value) {
            $htmls[$key] = $this->report->view([], $value)->render();
        }

        $this->templateWriteHtml($htmls, $options['template']);

        $this->makePdf();
    }

    /**
     * Make the PDF.
     */
    public function makePdf(): void
    {
        Storage::makeDirectory($dirname = $this->dirname());

        $this->filePath = $dirname.$this->basename().'.pdf';

        $this->mpdf->Output($this->fqpn(), Destination::FILE);
    }

    /**
     * Load and build Mpdf.
     */
    private function loadMpdf(array $parameters): MpdfBase
    {
        $parameters = array_merge($this->defaultParameters, $parameters);

        $this->mpdf = new MpdfBase($parameters);

        $this->mpdf->WriteHTML(file_get_contents(public_path('css/report.css')), 1);
        $this->mpdf->packTableData = true;
        $this->mpdf->use_kwt = true;
        $this->mpdf->shrink_tables_to_fit = 1;

        return $this->mpdf;
    }

    /**
     * Write the HTML files to the corresponding PDF page.
     */
    private function templateWriteHtml(array $htmls, string $template, array $context = []): void
    {
        //$this->mpdf->SetImportUse();

        $pageCount = $this->mpdf->SetSourceFile($template);

        for ($i = 1; $i <= $pageCount; $i++) {
            $this->mpdf->UseTemplate($this->mpdf->ImportPage($i));

            if (array_key_exists($i, $htmls)) {
                $this->mpdf->WriteHTML($htmls[$i], 2);
            }

            if ($i !== $pageCount) {
                $this->mpdf->AddPage();
            }
        }
    }
}
