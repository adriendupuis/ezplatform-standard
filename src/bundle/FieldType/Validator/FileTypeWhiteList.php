<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\Validator;

use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class FileTypeWhiteList extends FieldType\Validator
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var \finfo */
    private $mimeInfo;

    protected $constraints = [
        'fileTypeWhiteList' => [],
    ];

    protected $constraintsSchema = [
        'fileTypeWhiteList' => [
            'type' => 'array',
            'default' => [],
        ],
    ];

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
        $this->mimeInfo = new \finfo(FILEINFO_MIME);

        $this->constraints['fileTypeWhiteList'] = $this->configResolver->getParameter(
            'io.file_storage.file_type_whitelist'
        );
    }

    public function validateConstraints($constraints)
    {
        return [];
    }

    public function validate(FieldType\Value $value): bool
    {
        $path = $this->getFilePath($value);

        if (is_file($path) && !is_dir($path)) {
            $fileType = $this->getFileType($path);

            if (in_array($fileType, $this->constraints['fileTypeWhiteList'])) {
                return true;
            }

            $this->errors[] = new ValidationError(
                'A valid file is required. Following file type is not on the whitelist: %fileType%',
                null,
                [
                    '%fileType%' => $fileType,
                ],
                'fileTypeWhiteList'
            );

            return false;
        }

        $this->errors[] = new ValidationError(
            'A valid file is required. File has been invalidated early in the process; Its size might exceed '.ini_get('upload_max_filesize'),
            null,
            [],
            'fileTypeWhiteList'
        );

        return false;
    }

    public function getFilePath(FieldType\Value $value): string
    {
        if (isset($value->inputUri)) {
            return $value->inputUri; // tmp_name, the file hasn't been moved to storage yet
        }

        //TODO: Use abstraction; Handle DFS
        return "{$this->configResolver->getParameter('var_dir')}/{$this->configResolver->getParameter('storage_dir')}/original/{$value->id}";
    }

    public function getFileType(string $path)
    {
        return explode('; ', $this->mimeInfo->file($path))[0];
    }
}
