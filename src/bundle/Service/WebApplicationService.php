<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Service;

use AdrienDupuis\EzPlatformStandardBundle\Event\WebApplicationExtractionEvent;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\MVC\ConfigResolverInterface as ConfigResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @todo DFS
 * @todo Store usages in DB
 */
class WebApplicationService
{
    /** @var ConfigResolver */
    private $configResolver;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(ConfigResolver $configResolver, EventDispatcherInterface $eventDispatcher)
    {
        $this->configResolver = $configResolver;
        $this->eventDispatcher = $eventDispatcher;

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
        $extractionPath = $this->getDirectoryPath($originalPath);

        $this->setUsage($originalPath, $contentId, $versionId);

        if (!is_dir($extractionPath)) {
            //$this->eventDispatcher->dispatch(new BeforeWebApplicationExtractionEvent());
            $exception = $this->extract($originalPath, $extractionPath, $fileFieldValue->mimeType);
            $this->eventDispatcher->dispatch(new WebApplicationExtractionEvent(
                $content,
                $content->getField($fileFieldIdentifier, $languageCode),
                $originalPath,
                $extractionPath,
                is_null($exception) && is_dir($extractionPath)
            ));
            if (!is_null($exception)) {
                //throw $exception;
                return null;
            }
            if (!is_dir($extractionPath)) {
                return null;
            }
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
            $tmpIndexPath = "{$extractionPath}/{$indexRelativePath}";
        }
        if ($tmpIndexPath && is_file($tmpIndexPath)) {
            $indexPath = $tmpIndexPath;
        } else {
            foreach ([basename($originalPath), 'index.html'] as $indexRelativePath) {
                $tmpIndexPath = "{$extractionPath}/{$indexRelativePath}";
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

    private function extract(string $srcFilePath, string $destDirPath, string $mimeType = null): ?\Exception
    {
        try {
            if (!$mimeType) {
                $mimeType = (new \finfo(FILEINFO_MIME))->file($srcFilePath);
            }
            switch ($mimeType) {
                case 'application/zip':
                    $this->extractZip($srcFilePath, $destDirPath);
                    break;
                case 'application/x-tar':
                    $this->extractTar($srcFilePath, $destDirPath);
                    break;
                case 'application/x-gzip':
                    $this->extractGzip($srcFilePath, $destDirPath);
                    break;
                case 'text/html':
                case 'text/xml':
                    if (!copy($srcFilePath, "$destDirPath/".basename($srcFilePath))) {
                        throw new \Exception('Uncopyable file');
                    }
                    // no break
                default:
                    throw new \Exception("Unsupported file type '$mimeType'");
            }
        } catch (\Exception $exception) {
            return $exception;
        }

        return null;
    }

    private function extractZip($srcFile, $destDir): void
    {
        $zip = new \ZipArchive();
        $zip->open($srcFile);
        $zip->extractTo($destDir);
        $zip->close();
    }

    private function extractTar($srcFile, $destDir): void
    {
        $tar = new \PharData($srcFile);
        $tar->extractTo($destDir);
    }

    private function extractGzip($srcFile, $destDir): void
    {
        $extension = 'tmp';
        $gzip = new \PharData($srcFile);
        $tar = $gzip->decompress($extension);
        $tar->extractTo($destDir);
        unlink(str_replace(pathinfo($srcFile, PATHINFO_EXTENSION), $extension, $srcFile));
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
