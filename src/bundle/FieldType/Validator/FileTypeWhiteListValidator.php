<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\Validator;

use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Validator\FileExtensionBlackListValidator;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class FileTypeWhiteListValidator extends FieldType\Validator
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /* @var FileExtensionBlackListValidator */
    private $fileExtensionBlackListValidator;

    /** @var \finfo */
    private $mimeInfo;

    protected $constraints = [
        'extensionsBlackList' => [],
        'fileTypeWhiteList' => [],
    ];

    protected $constraintsSchema = [
        'fileTypeWhiteList' => [
            'type' => 'array',
            'default' => [],
        ],
    ];

    /** @var string */
    private $root = '.';

    public function __construct(ConfigResolverInterface $configResolver, FileExtensionBlackListValidator $fileExtensionBlackListValidator, array $config)
    {
        $this->configResolver = $configResolver;
        $this->fileExtensionBlackListValidator = $fileExtensionBlackListValidator;
        $this->mimeInfo = new \finfo(FILEINFO_MIME);

        $this->constraints['extensionsBlackList'] = $this->fileExtensionBlackListValidator->extensionsBlackList;
        $this->constraints['fileTypeWhiteList'] = $this->configResolver->getParameter('io.file_storage.file_type_whitelist');
        $this->constraintsSchema = array_merge(
            $this->fileExtensionBlackListValidator->getConstraintsSchema(),
            $this->constraintsSchema
        );
        if (array_key_exists('root', $config)) {
            $this->root = rtrim($config['root'], '/');
        }
    }

    public function validateConstraints($constraints)
    {
        return [];
    }

    public function validate(FieldType\Value $value): bool
    {
        if (!$this->fileExtensionBlackListValidator->validate($value)) {
            $this->errors = $this->fileExtensionBlackListValidator->getMessage();

            return false;
        }

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

    /**
     * @param FieldType\BinaryBase\Value|FieldType\Image\Value $value
     */
    public function getFilePath(FieldType\Value $value): string
    {
        if (isset($value->inputUri)) {
            return $value->inputUri; // tmp_name, the file hasn't been moved to storage yet
        }

        if (isset($value->uri)) {
            return "{$this->root}{$value->uri}";
        }

        return trim(shell_exec("find {$this->root}/{$this->configResolver->getParameter('var_dir')}/{$this->configResolver->getParameter('storage_dir')} -path */{$value->id}"));
    }

    public function getFileType(string $path)
    {
        return explode('; ', $this->mimeInfo->file($path))[0];
    }
}
