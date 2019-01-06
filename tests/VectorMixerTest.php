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
            ]
        ];
    }

    public function testVectorMixetResultsCount() {
        $arr1 = range(1, 10);
        $arr2 = range(1, 10);
        $mixer = new VectorMixer($arr1, $arr2);
        $mixer->rewind();
        $countResults = 0;

        while ($mixer->valid()) {
            $mixer->next();
            $countResults++;
        }

        $expectedCountResult = 184756; // = 20! / (10! * 10!)
        assertEquals($expectedCountResult, $countResults);
    }
}
