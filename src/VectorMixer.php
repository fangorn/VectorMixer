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

    public function rewind(): void {
        $this->currentPattern = $this->getFirstPattern();
    }

    public function valid(): bool {
        return $this->currentPattern !== null;
    }

    public function next(): void {

        if ($this->currentPattern !== '') {
            $this->currentPattern = $this->getNextPattern();
            return;
        }

        $this->currentPattern = null;
    }

    public function current(): array {
        return $this->mixArraysByCurrentPattern();
    }

    private function getFirstPattern(): string {
        return str_repeat('0', count($this->recipient)) . str_repeat('1', count($this->donor));
    }

    private function addOneToBinaryNumber(string $binaryNumber): string {
        $result = $binaryNumber;

        $i = strlen($result) - 1;
        while ($result[$i] === '1' && $i >= 0) {
            $result[$i] = '0';
            $i--;
        }
        $result[$i] = '1';

        return $result;
    }

    private function getNextPattern(): ?string {
        $patternLength      = count($this->recipient) + count($this->donor);
        $maxPattern = str_repeat('1', $patternLength);
        $possiblePattern = $this->currentPattern;

        while ($possiblePattern < $maxPattern) {

            // TODO: оптимизировать
            //$possiblePattern = decbin(bindec($possiblePattern) + 1);
            $possiblePattern = $this->addOneToBinaryNumber($possiblePattern);

            if (substr_count($possiblePattern, '1') === count($this->donor)) {
                while (strlen($possiblePattern) < $patternLength) {
                    $possiblePattern = '0' . $possiblePattern;
                }
                return $possiblePattern;
            }
        }
        return null;
    }

    private function mixArraysByCurrentPattern(): array {

        $donor      = $this->donor;
        $recipient  = $this->recipient;
        $mixedArray = [];

        for ($i = 0; $i < strlen($this->currentPattern); $i++) {
            $currentDigit = $this->currentPattern[$i];
            if ($currentDigit === '1') {
                $mixedArray[] = array_shift($donor);
            } else {
                $mixedArray[] = array_shift($recipient);
            }
        }
        return $mixedArray;
    }
}

