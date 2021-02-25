<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Service;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\MVC\ConfigResolverInterface as ConfigResolver;

/**
 * @todo DFS
 * @todo Store usages in DB
 */
class WebApplicationService
{
    public function __construct(ConfigResolver $configResolver)
    {
        $this->configResolver = $configResolver;
        $this->storagePath = "{$this->configResolver->getParameter('var_dir')}/{$this->configResolver->getParameter('storage_dir')}";
        $this->webApplicationStoragePath = "{$this->storagePath}/images/web_application";
        $this->usageStorageFilePath = "{$this->webApplicationStoragePath}/{$this->usageStorageFile}";
        if (!is_dir($this->webApplicationStoragePath)) {
            mkdir($this->webApplicationStoragePath, 0755, true);
        }
        $this->loadUsages();
    }

    /**
     * @param string      $fileFieldIdentifier        identifier of the BinaryFile (ezbinaryfile) field container the application page or orchive
     * @param string|null $indexFieldIdentifierOrPath identifier of the TextLine (ezstring) field containing the relative index path, or the index path itself; if null,
     * @param null        $languageCode
     *
     * @throws Exception
     */
    public function getWebApplicationUrl(Content $content, string $fileFieldIdentifier, string $indexFieldIdentifierOrPath = null, string $languageCode = null): ?string
    {
        /** @var FieldType\BinaryFile\Value $fileFieldValue */
        $fileFieldValue = $content->getFieldValue($fileFieldIdentifier, $languageCode);

        $contentId = $content->id;
        $versionId = $content->versionInfo->id;

        $originalPath = "{$this->storagePath}/original/{$fileFieldValue->id}";
        $dirPath = $this->getDirectoryPath($originalPath);

        $this->setUsage($originalPath, $contentId, $versionId);

        if (!is_dir($dirPath)) {
            $exception = $this->extract($originalPath, $dirPath, $fileFieldValue->mimeType);
            if (!is_null($exception)) {
                //dump($exception);
                //throw $exception;
                return null;
            }
        }
        if (!is_dir($dirPath)) {
            return null;
        }

        $indexPath = null;
        $tmpIndexPath = null;
        $indexRelativePath = null;
        $indexFieldValue = null;

        if ($indexFieldIdentifierOrPath) {
            /** @var FieldType\TextLine\Value $indexFieldValue */
            $indexFieldValue = $content->getFieldValue($indexFieldIdentifierOrPath, $languageCode);
        }
        if ($indexFieldValue) {
            $indexRelativePath = $indexFieldValue->text;
        } elseif ($indexFieldIdentifierOrPath) {
            $indexRelativePath = $indexFieldIdentifierOrPath;
        }
        if ($indexRelativePath) {
            $tmpIndexPath = "{$dirPath}/{$indexRelativePath}";
        }
        if ($tmpIndexPath && is_file($tmpIndexPath)) {
            $indexPath = $tmpIndexPath;
        } else {
            foreach ([basename($originalPath), 'index.html'] as $indexRelativePath) {
                $tmpIndexPath = "{$dirPath}/{$indexRelativePath}";
                if (is_file($tmpIndexPath)) {
                    $indexPath = $tmpIndexPath;
                    break;
                }
            }
        }
        if (!$indexPath) {
            return null;
        }

        $baseUrl = '';
        $webApplicationUrl = "$baseUrl/$indexPath";

        return $webApplicationUrl;
    }

    public function onDelete($contentId, $versionId = null): void
    {
        $fileHashes = $this->getUsageFileHashes($contentId, $versionId);
        $this->removeUsages($contentId, $versionId);
        foreach ($fileHashes as $fileHash) {
            $this->removeIfNotUsed($fileHash);
        }
    }

    private function extract(string $originalPath, string $dirPath, string $mimeType = null): ?\Exception
    {
        if (!$mimeType) {
            $mimeType = (new \finfo(FILEINFO_MIME))->file($originalPath);
        }
        dump($originalPath, $dirPath, $mimeType);
        switch ($mimeType) {
            case 'application/zip':
                return $this->extractZip($originalPath, $dirPath);
            case 'application/x-tar':
                return $this->extractTar($originalPath, $dirPath);
            case 'application/x-gzip':
                return $this->extractGzip($originalPath, $dirPath);
            case 'text/html':
            case 'text/xml':
                try {
                    if (!copy($originalPath, "$dirPath/".basename($originalPath))) {
                        throw new \Exception('Uncopyable file');
                    }
                } catch (\Exception $exception) {
                    return $exception;
                }
            default:
                return new \Exception('Unsupported file type');
        }
    }

    private function extractZip($srcFile, $destDir): ?\Exception
    {
        try {
            $zip = new \ZipArchive();
            $zip->open($srcFile);
            $zip->extractTo($destDir);
            $zip->close();
        } catch (\Exception $exception) {
            return $exception;
        }

        return null;
    }

    private function extractTar($srcFile, $destDir): ?\Exception
    {
        try {
            dump(shell_exec("tar -tvf $srcFile"));
            $tar = new \PharData($srcFile);
            $tar->extractTo($destDir);
        } catch (\Exception $exception) {
            return $exception;
        }

        return null;
    }

    private function extractGzip($srcFile, $destDir): ?\Exception
    {
        $extension = 'tmp';
        try {
            $gzip = new \PharData($srcFile);
            $tar = $gzip->decompress($extension);
            $tar->extractTo($destDir);
            unlink(str_replace(pathinfo($srcFile, PATHINFO_EXTENSION), $extension, $srcFile));
        } catch (\Exception $exception) {
            return $exception;
        }

        return null;
    }

    private function getDirectoryPath($filePath): string
    {
        return $this->getHashDirectoryPath($this->getFileHash($filePath));
    }

    private function getFileHash($filePath): string
    {
        return hash_file('md5', $filePath);
    }

    private function getHashDirectoryPath($fileHash): string
    {
        return "{$this->webApplicationStoragePath}/{$fileHash}";
    }

    private function removeIfNotUsed($fileHash): void
    {
        if (!$this->isUsed($fileHash)) {
            $this->removeDirectory($this->getHashDirectoryPath($fileHash));
        }
    }

    private function removeDirectory($dirPath): void
    {
        $files = glob($dirPath.'/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($dirPath);
    }

    private $usages = [];
    private $usageStorageFile = 'usages.csv';
    private $usageStorageFilePath;
    private $usageStorageFileLineColumnSeparator = ',';

    private function loadUsages(): void
    {
        if (is_file($this->usageStorageFilePath)) {
            $this->usages = file($this->usageStorageFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } else {
            $this->usages = [];
            touch($this->usageStorageFilePath);
        }
    }

    private function getUsageEntry(?string $fileHash, int $contentId, ?int $versionId): string
    {
        $s = $this->usageStorageFileLineColumnSeparator;

        return "$fileHash{$s}$contentId{$s}$versionId";
    }

    private function isUsed($fileHash): bool
    {
        $s = $this->usageStorageFileLineColumnSeparator;
        foreach ($this->usages as $usage) {
            if (0 === strpos("$fileHash{$s}", $usage)) {
                return true;
            }
        }

        return false;
    }

    private function getUsageFileHashes(int $contentId, int $versionId = null): array
    {
        $s = $this->usageStorageFileLineColumnSeparator;
        $entry = $this->getUsageEntry(null, $contentId, $versionId);
        $hashes = [];
        foreach ($this->usages as $index => $usage) {
            if (false !== strpos($usage, $entry)) {
                $hashes[] = explode($s, $usage)[0];
            }
        }

        return $hashes;
    }

    private function setUsage(string $originalPath, int $contentId, int $versionId)
    {
        $entry = $this->getUsageEntry($this->getFileHash($originalPath), $contentId, $versionId);
        if (false === array_search($entry, $this->usages)) {
            $this->usages[] = $entry;
            file_put_contents($this->usageStorageFilePath, PHP_EOL.$entry, FILE_APPEND);
        }
    }

    private function removeUsages(int $contentId, int $versionId = null)
    {
        $entry = $this->getUsageEntry(null, $contentId, $versionId);
        foreach (array_reverse($this->usages) as $rIndex => $usage) {
            if (false !== strpos($usage, $entry)) {
                $index = count($this->usages) - $rIndex - 1;
                array_splice($this->usages, $index, 1);
            }
        }
        $this->storeUsages();
    }

    private function storeUsages()
    {
        file_put_contents($this->usageStorageFilePath, trim(implode(PHP_EOL, $this->usages)));
    }
}
