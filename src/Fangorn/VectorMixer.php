<?php

namespace Fangorn;

class VectorMixer {

    /** @var int */
    private $currentElemNumber = 0;
    /** @var array */
    private $mixedElems = [];

    public function __construct(array $recipient, array $donor)
    {
        $this->mixedElems = [];

        if (count($recipient) === 0) {
            array_push($this->mixedElems, $donor);
            return;
        }
        if (count($donor) === 0) {
            array_push($this->mixedElems, $recipient);
            return;
        }

        $combinationPatterns = $this->getCombinationPatterns(count($recipient), count($donor));
        foreach($combinationPatterns as $pattern) {
            $mixedCombination = $this->mixIt($recipient, $donor, $pattern);
            array_push($this->mixedElems, $mixedCombination);
        }
    }

    private function getCombinationPatterns(int $recipientsLength, int $donorsLength): array {
        $patternLength = $recipientsLength + $donorsLength;
        $maxPatternsCount = pow(2, $recipientsLength + $donorsLength);
        $patterns = [];
        for($i = 0; $i < $maxPatternsCount; $i++) {
            $possiblePattern = decbin($i);
            if (substr_count($possiblePattern, '1') === $donorsLength) {
                while (strlen($possiblePattern) < $patternLength) {
                    $possiblePattern = '0' . $possiblePattern;
                }
                array_push($patterns, $possiblePattern);
            }
        }
        return $patterns;
    }

    private function mixIt(array $arr0, array $arr1, string $pattern): array {
        $mixedArray = [];
        for ($i = 0; $i < strlen($pattern); $i++) {
            $currentDigit = $pattern[$i];
            if ($currentDigit === '1') {
                array_push($mixedArray, array_shift($arr1));
            } else {
                array_push($mixedArray, array_shift($arr0));
            }
        }
        return $mixedArray;
    }

    public function rewind(): void {
        $this->currentElemNumber = 0;
    }

    public function valid(): bool {
        return isset($this->mixedElems[$this->currentElemNumber]);
    }

    public function next(): void {
        $this->currentElemNumber++;
    }

    public function current(): array {
        return $this->mixedElems[$this->currentElemNumber];
    }
}

