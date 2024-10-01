<?php

namespace App\Enums;

enum MediaCollection: string
{
    case GENERIC = 'GENERIC';
    case EVIDENCE_PHOTO = 'EVIDENCE_PHOTO';

    public function supportedMimeTypes(): array
    {
        return match ($this) {
            self::GENERIC => [
                'image/png',
                'image/jpeg',
                'image/jpg',
                'image/gif',
                'image/webp',
                'image/avif',
                'image/heic',
                'image/heif',
                'application/pdf',
            ],
            self::EVIDENCE_PHOTO => [
                'image/png',
                'image/jpeg',
                'image/jpg',
                'image/gif',
                'image/webp',
                'image/avif',
                'image/heic',
                'image/heif',
            ],
        };
    }

    public function supportedExtensions(): array
    {
        return match ($this) {
            self::GENERIC => [
                'png',
                'jpeg',
                'jpg',
                'gif',
                'webp',
                'avif',
                'heic',
                'pdf',
            ],
            self::EVIDENCE_PHOTO => [
                'png',
                'jpeg',
                'jpg',
                'gif',
                'webp',
                'avif',
                'heic',
            ]
        };
    }

    // public function attributeOptionName(): AttributeOptionNames
    // {
    //     return match ($this) {
    //         self::HI_DOCUMENTATION,
    //         self::WHOLE_BODY_DORSAL_FIN,
    //         self::MONITORING_REPORTS => AttributeOptionNames::UPLOADS_PHOTO_TAG,
    //         self::NRT_REPORT,
    //         self::PDF_REPORT,
    //         self::PROCEDURE_REPORTS,
    //         self::SAMPLE_RESULT_REPORT_DOCUMENTS => AttributeOptionNames::UPLOADS_DOCUMENT_TAG,
    //         self::NECROPSY_PHOTOS => AttributeOptionNames::UPLOADS_ALL_TAGS,
    //     };
    // }

    public function limitCount(): int
    {
        return match ($this) {
            self::GENERIC,
            self::EVIDENCE_PHOTO => 10,
        };
    }
}
