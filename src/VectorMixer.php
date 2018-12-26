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
            $this->getNextPattern();
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

    private function swap(string $string, int $index1, int $index2) {
        $result = $string;
        $buffer = $result[$index1];
        $result[$index1] = $result[$index2];
        $result[$index2] = $buffer;
        return $result;
    }

    /**
     * Алгоримт получения следующей перестановки с повторениями
     */
    private function getNextPattern(): void {

        $possiblePattern = $this->currentPattern;

        $j = strlen($possiblePattern) - 2;
        while ($j !== -1 && $possiblePattern[$j] >= $possiblePattern[$j + 1]) {
            $j--;
        }
        if ($j === -1) {
            $this->currentPattern = null;
        }
        $k = strlen($possiblePattern) - 1;
        while ($possiblePattern[$j] >= $possiblePattern[$k]) {
            $k--;
        }

        $possiblePattern = $this->swap($possiblePattern, $j, $k);
        $l = $j + 1;
        $r = strlen($possiblePattern) - 1;
        while ($l < $r) {
            $possiblePattern = $this->swap($possiblePattern, $l++, $r--);
        }
        $this->currentPattern = $possiblePattern;
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
