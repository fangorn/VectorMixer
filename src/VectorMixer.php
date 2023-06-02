<?php

namespace VectorMixer;

/**
 * Класс, замешивающий два вектора с сохранением относительного порядка элеменов векторов.
 * Сохранение порядка подразумевает, что второй элемент одного из векторов будет следовать за его первый элементом.
 * Между ними могут оказаться элемент(-ы) другого вектора, но второй элемент одного вектора не может идти до его первого.
 *
 * Вектор можно представить как набор точек в заказе на доставку, в котором курьеру следует посетить точки в заданном порядке, например,
 * сначала выполнить pickUp1 и отвезти на dropOff1. Предлагаем курьеру взять еще заказ, при этом у него есть несколько вариантов:
 * 1) pickUp1 => dropOff1 => pickUp2 => dropOff2;
 * 2) pickUp1 => pickUp2 => dropOff1 => dropOff2;
 * ...
 * Не может оказаться следующих схем: ... dropOff1 => ... => pickUp1 => ...
 */
class VectorMixer {
    /** @var int[] */
    private array $recipient;

    /** @var int[] */
    private array $donor;

    /**
     * Шаблон для смешения двух векторов представляет собой строку, состоящую из нулей и единиц.
     * Количество нулей в шаблоне равно количеству элементов в $recipient, а количество единиц равно количеству
     * элементов в $donor. Если какой-либо вектор пуст, соответствующих ему цифр в шаблоне нет. Если оба вектора
     * пусты - в качестве шаблона берется пустая строка.
     * @example $recipient = [3]; $donor = [1, 2]; шаблон, например, такой: $pattern = '101'; результат смешивания: [1, 3, 2].
     */
    private ?string $currentPattern;

    /**
     * @param int[] $recipient
     * @param int[] $donor
     */
    public function __construct(array $recipient, array $donor) {
        $this->recipient      = $recipient;
        $this->donor          = $donor;
        $this->currentPattern = $this->getFirstPattern();
    }

    /**
     * Устанавливает шаблон в исходное состояние: слева нули, справа единицы
     */
    public function rewind(): void {
        $this->currentPattern = $this->getFirstPattern();
    }

    public function isValidPattern(): bool {
        return $this->currentPattern !== null;
    }

    /**
     * Формирует следующий шаблон, если возможно
     */
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

    private function swapSymbols(string $string, int $index1, int $index2): string {
        $result = $string;
        $buffer = $result[$index1];

        $result[$index1] = $result[$index2];
        $result[$index2] = $buffer;

        return $result;
    }

    /**
     * Алгоритм получения следующей перестановки с повторениями
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

        $leftSymbol  = $j + 1;
        $rightSymbol = strlen($possiblePattern) - 1;
        while ($leftSymbol < $rightSymbol) {
            $possiblePattern = $this->swapSymbols($possiblePattern, $leftSymbol++, $rightSymbol--);
        }

        $this->currentPattern = $possiblePattern;
    }
}
