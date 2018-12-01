<?php

namespace Fangorn;

use PHPUnit\Framework\TestCase;

class VectorMixerTest extends TestCase {
    /**
     * Проверка замешивателя векторов.
     * @dataProvider arraysProvider()
     */
    public function testVectorMixer(array $recipient, array $donor, array $expectedResult) {
        $mixer = new VectorMixer($recipient, $donor);
        $mixer->rewind();
        $elements = [];
        $expectedLength = count($recipient) + count($donor);
        while ($mixer->valid()) {
            $element = $mixer->current();
            assertCount($expectedLength, $element);
            $elements[] = $element;
            $mixer->next();
        }
        sort($elements);
        assertEquals($expectedResult, array_values($elements));
    }

    public function arraysProvider() {
        return [
            [
                'recipient'         => [],
                'donor'             => [],
                'expectedResult'    => [[]],
            ],
            [
                'recipient'         => [],
                'donor'             => [1, 2],
                'expectedResult'    => [[1, 2]],
            ],
            [
                'recipient'         => [1, 2],
                'donor'             => [],
                'expectedResult'    => [[1, 2]],
            ],
            [
                'recipient'         => [1],
                'donor'             => [2, 3],
                'expectedResult'    => [[1, 2, 3], [2, 1, 3], [2, 3, 1]],
            ],
            [
                'recipient'         => [1, 2],
                'donor'             => [3],
                'expectedResult'    => [[1, 2, 3], [1, 3, 2], [3, 1, 2]],
            ],
            [
                'recipient'         => [1, 2],
                'donor'             => [3, 4],
                'expectedResult'    => [[1, 2, 3, 4], [1, 3, 2, 4], [1, 3, 4, 2], [3, 1, 2, 4], [3, 1, 4, 2], [3, 4, 1, 2]],
            ],
        ];
    }
}
