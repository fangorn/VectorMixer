<?php

namespace Fangorn;

class VectorMixer {

    /** @var array */
    private $recipient;

    /** @var array */
    private $donor;

    /**
     * Шаблон для смешения двух векторов представляет собой строку, состоящую из нулей и единиц.
     * Количество нулей в шаблоне равно количеству элементов в $recipient, а количество единиц равно количеству
     * элементов в $donor. Если какой-либо вектор пуст, соответствующих ему цифр в шаблоне нет. Если оба вектора
     * пусты - в качестве шаблона берется пустая строка.
     * @var string|null
     * @example $recipient = [3]; $donor = [1, 2]; тогда $pattern может быть, например, таким: $pattern = '101';
     */
    private $currentPattern;

    public function __construct(array $recipient, array $donor)
    {
        $this->recipient        = $recipient;
        $this->donor            = $donor;
        $this->currentPattern   = $this->getFirstPattern();
    }

    /**
     * Устанавливает шаблон в исходное состояние: слева нули, справа единицы
     */
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

    /**
     * Смешивает вектора $recipient и $donor по текущему шаблону
     * @example $recipient = [3]; $donor = [1, 2]; $pattern = '101';
     * тогда результатом будет: $mixedArray = [1, 3, 2];
     */
    public function current(): array {

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

    private function getFirstPattern(): string {
        return str_repeat('0', count($this->recipient)) . str_repeat('1', count($this->donor));
    }

    private function swapSymbols(string $string, int $index1, int $index2) {
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
            return;
        }
        $k = strlen($possiblePattern) - 1;
        while ($possiblePattern[$j] >= $possiblePattern[$k]) {
            $k--;
        }

        $possiblePattern = $this->swapSymbols($possiblePattern, $j, $k);
        $l = $j + 1;
        $r = strlen($possiblePattern) - 1;
        while ($l < $r) {
            $possiblePattern = $this->swapSymbols($possiblePattern, $l++, $r--);
        }
        $this->currentPattern = $possiblePattern;
    }
}
