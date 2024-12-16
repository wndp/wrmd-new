<?php

namespace App\Reporting\Generators;

use App\Reporting\Contracts\Generator;
use Illuminate\Support\Facades\Storage;
use Zebra\Client;
use Zebra\CommunicationException;
use Zebra\Zpl\Builder;

class Zebra extends Generator
{
    /**
     * The ZPL command builder.
     *
     * @var \Zebra\Zpl\Builder
     */
    public $zpl;

    /**
     * The default DPI of the printer.
     *
     * @var int
     */
    public $dpi = 203;

    /**
     * The printers default label width in inches.
     *
     * @var int
     */
    public $defaultWidth = 4;

    /**
     * Override the temporaryUrl method to instead return a status response.
     */
    public function temporaryUrl(): StdClass
    {
        return (object) $this->response;
    }

    /**
     * Generate the report.
     */
    public function handle(): void
    {
        $this->zpl = new Builder;

        if (array_key_exists('homePosition', $this->report->options())) {
            $this->setLabelHomePosition($this->report->options()['homePosition']);
        }

        foreach ($this->report->data() as $command) {
            $function = array_shift($command);
            $this->zpl->$function(...$command);
        }

        if (app()->runningUnitTests()) {
            Storage::makeDirectory($dirname = $this->dirname());
            $this->filePath = $dirname.$this->basename().'.txt';
            file_put_contents($this->fqpn(), $this->zpl);

            return;
        }

        return $this->sendZplCommand(
            $this->report->ipAddress(),
            $this->report->port()
        );
    }

    /**
     * Set the label home position along the x axis using the labels width in inches.
     */
    public function setLabelHomePosition(float $labelWidth): void
    {
        $this->zpl->lh(
            ($this->defaultWidth / 2) - ($labelWidth / 2) * $this->dpi,
            0
        );
    }

    /**
     * Send a \Zebra\Zpl\Builder command to a zebra printer.
     */
    private function sendZplCommand(string $ipAddress, string $port): void
    {
        try {
            $client = new Client(
                $ipAddress,
                $port
            );

            //socket_set_nonblock($this->socket);

            $client->send($this->zpl);

            $this->response = [
                'success' => true,
                'message' => '',
            ];
        } catch (CommunicationException $e) {
            $this->response = [
                'success' => false,
                'message' => 'There was a problem communicating with the Zebra printer. Verify the printers IP address and port on the event configuration page.',
            ];
        }
    }
}
