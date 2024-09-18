<?php

namespace App\Reporting;

use fpdi_pdf_parser;
use InvalidArgumentException;
use pdf_context;

class FpdiPdfParserAdapter extends fpdi_pdf_parser
{
    /**
     * Constructor.
     * A combination of fpdi_pdf_parser and pdf_parser with the $context argument added.
     */
    public function __construct(string $filename, array $context = [])
    {
        $this->filename = $filename;

        $this->_f = @fopen($this->filename, 'rb', null, $context);

        if (! $this->_f) {
            throw new InvalidArgumentException(sprintf('Cannot open %s !', $filename));
        }

        $this->getPdfVersion();

        if (! class_exists('pdf_context')) {
            require_once 'pdf_context.php';
        }
        $this->_c = new pdf_context($this->_f);

        // Read xref-Data
        $this->_xref = [];
        $this->_readXref($this->_xref, $this->_findXref());

        // Check for Encryption
        $this->getEncryption();

        // Read root
        $this->_readRoot();

        // resolve Pages-Dictonary
        $pages = $this->resolveObject($this->_root[1][1]['/Pages']);

        // Read pages
        $this->_readPages($pages, $this->_pages);

        // count pages;
        $this->_pageCount = count($this->_pages);
    }
}
