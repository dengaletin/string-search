<?php

namespace SidSpears\StringSearcher;

use Hoa\File\Read as HoaStream;
use Hoa\Mime\Mime;
use Symfony\Component\Yaml\Yaml;

class FileValidator
{
    protected $config;

    protected $validations = [
        'mimetype',
        'max_filesize'
    ];

    protected $errorMessages = [];

    public function __construct(string $userConfigPath = null)
    {
        $configPath = $userConfigPath ?? __DIR__ . '/../config.yaml';
        $this->config = Yaml::parseFile($configPath);
    }

    public function isValid(string $filePath): bool
    {
        $isValid = true;

        foreach ($this->validations as $validationKey) {
            $funcName = 'validate' . $this->camelize($validationKey);
            $configValue = $this->config[$validationKey];

            if (!is_null($configValue)) {
                $isValid = $this->$funcName($filePath, $configValue) && $isValid;
            }
        }

        return $isValid;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    protected function validateMimetype(string $filePath, string $configValue): bool
    {
        $mimeType = (new Mime(new HoaStream($filePath)))->getMime();

        if (($isValid = $mimeType === $configValue) === false) {
            $this->errorMessages[] = "Mimetype is not valid. Yours: $mimeType, must: $configValue";
        }

        return $isValid;
    }

    protected function validateMaxFilesize(string $filePath, string $configValue): bool
    {
        $fileSize = filesize($filePath);

        if (($isValid = $fileSize <= $configValue) === false) {
            $this->errorMessages[] = "Big file. Yours: $fileSize, must: $configValue";
        }

        return $isValid;
    }

    protected function camelize(string $input): string
    {
        return str_replace('_', '', ucwords($input, '_'));
    }
}