<?php

namespace Fangorn;

class VectorMixer {

    /** @var array */
    private $recipient;

    /** @var array */
    private $donor;

    /** @var string|null */
    private $currentPattern;

    public function __construct(array $recipient, array $donor)
    {
        $this->recipient        = $recipient;
        $this->donor            = $donor;
        $this->currentPattern   = $this->getFirstPattern();
    }

    private function getFirstPattern(): string {
        return $this->getPattern(0);
    }

    private function getNextPattern(): string {
        $startSearchPosition = bindec($this->currentPattern) + 1;
        return $this->getPattern($startSearchPosition);
    }

    private function getPattern(int $startSearchPosition): string {

        $patternLength      = count($this->recipient) + count($this->donor);
        $maxPatternsCount   = pow(2, $patternLength);

        for ($i = $startSearchPosition; $i < $maxPatternsCount; $i++) {
            $possiblePattern = decbin($i);
            if (substr_count($possiblePattern, '1') === count($this->donor)) {
                while (strlen($possiblePattern) < $patternLength) {
                    $possiblePattern = '0' . $possiblePattern;
                }
                return $possiblePattern;
            }
        }
        return '';
    }

    private function mixArraysByCurrentPattern(): array {

        if (empty($this->currentPattern)) {
            return [];
        }

        $donor      = $this->donor;
        $recipient  = $this->recipient;
        $mixedArray = [];

        for ($i = 0; $i < strlen($this->currentPattern); $i++) {
            $currentDigit = $this->currentPattern[$i];
            if ($currentDigit === '1') {
                array_push($mixedArray, array_shift($donor));
            } else {
                array_push($mixedArray, array_shift($recipient));
            }
        }
        return $mixedArray;
    }

    public function rewind(): void {
        $this->currentPattern = $this->getFirstPattern();
    }

    public function valid(): bool {
        return $this->currentPattern !== '';
    }

    public function next(): void {
        $this->currentPattern = $this->getNextPattern();
    }

    public function current(): array {
        return $this->mixArraysByCurrentPattern();
    }
}

