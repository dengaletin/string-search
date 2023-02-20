<?php

namespace SidSpears\StringSearcher;

class StringSearcher
{
    protected $fileValidator;

    public function __construct(string $userConfigPath = null)
    {
        $this->fileValidator = new FileValidator($userConfigPath);
    }

    public function findStringAndPosition(string $filePath, string $needle): ?array
    {
        if ($this->fileValidator->isValid($filePath)) {
            $handle = fopen($filePath, 'r');
            $stringNum = 1;

            while (($buffer = fgets($handle)) !== false) {
                if ($foundedPosition = mb_strpos($buffer, $needle)) {
                    $result = [
                        'string' => $stringNum,
                        'position' => $foundedPosition
                    ];
                    break;
                }

                $stringNum++;
            }
        } else {
            $result = ['errors' => $this->fileValidator->getErrorMessages()];
        }

        return $result ?? null;
    }
}