<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SidSpears\StringSearcher\StringSearcher;
use Symfony\Component\Yaml\Yaml;

final class StringSearcherTest extends TestCase
{
    CONST CONFIG_FILE_PATH = '_temp_config.yaml';
    CONST SEARCHED_FILE_PATH = '_temp_text.txt';

    /**
     * @dataProvider findStringAndPositionCases
     */
    public function testFindStringAndPosition(?array $configContent, string $searchedString, ?array $expectedResult)
    {
        $this->createSearchedFile();

        if ($configContent) {
            $this->createConfigFile($configContent);
            $stringSearcher = new StringSearcher(self::CONFIG_FILE_PATH);
        } else {
            $stringSearcher = new StringSearcher();
        }

        $this->assertEquals(
            $expectedResult,
            $stringSearcher->findStringAndPosition(self::SEARCHED_FILE_PATH, $searchedString)
        );

        $this->clear();
    }

    private function createConfigFile(?array $configContent)
    {
        $configFileHandle = fopen(self::CONFIG_FILE_PATH, 'w');
        fwrite($configFileHandle, Yaml::dump($configContent));
    }

    private function createSearchedFile()
    {
        $searchedFileHandle = fopen(self::SEARCHED_FILE_PATH, 'w');
        $fileContent = "first string\nsecond string\nthird string\nforth string";
        fwrite($searchedFileHandle, $fileContent);
    }

    private function clear()
    {
        unlink(self::SEARCHED_FILE_PATH);
        if (file_exists(self::CONFIG_FILE_PATH)){
            unlink(self::CONFIG_FILE_PATH);
        }
    }

    public function findStringAndPositionCases()
    {
        $standartString = 'ird str';
        $notFoundString = 'randomtext';
        $errors = [
            'mime' => 'Mimetype is not valid. Yours: text/plain, must: text/css',
            'filesize' => 'Big file. Yours: 52, must: 1'
        ];

        return [
            'Without config file' => [
                'configContent' => null,
                'searchedString' => $standartString,
                'expectedResult' => ['string' => 3, 'position' => 2]
            ],
            'Config without restricts' => [
                'configContent' => ['mimetype' => null, 'max_filesize' => null],
                'searchedString' => $standartString,
                'expectedResult' => ['string' => 3, 'position' => 2]
            ],
            'Mime type error' => [
                'configContent' => ['mimetype' => 'text/css', 'max_filesize' => null],
                'searchedString' => $standartString,
                'expectedResult' => ['errors' => [$errors['mime']]]
            ],
            'Max filesize error' => [
                'configContent' => ['mimetype' => null, 'max_filesize' => 1],
                'searchedString' => $standartString,
                'expectedResult' => ['errors' => [$errors['filesize']]]
            ],
            'Both errors' => [
                'configContent' => ['mimetype' => 'text/css', 'max_filesize' => 1],
                'searchedString' => $standartString,
                'expectedResult' => ['errors' => [$errors['mime'], $errors['filesize']]]
            ],
            'Substring not found' => [
                'configContent' => null,
                'searchedString' => $notFoundString,
                'expectedResult' => null
            ],
        ];
    }
}
