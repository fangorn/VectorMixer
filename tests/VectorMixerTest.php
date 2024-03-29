<?php

namespace VectorMixer;

use PHPUnit\Framework\TestCase;

class VectorMixerTest extends TestCase {
    /**
     * Проверка замешивателя векторов.
     * @dataProvider mixVectorsDataProvider()
     */
    public function testVectorMixer(array $recipient, array $donor, array $expectedResult): void {
        $mixer = new VectorMixer($recipient, $donor);
        $mixer->rewind();
        $elements = [];
        $expectedLength = count($recipient) + count($donor);
        while ($mixer->isValidPattern()) {
            $element = $mixer->current();
            self::assertCount($expectedLength, $element);
            $elements[] = $element;
            $mixer->next();
        }
        sort($elements);
        self::assertEquals($expectedResult, array_values($elements));
    }

    public static function mixVectorsDataProvider(): array {
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

    /**
     * Проверим количество генерируемых вариантов.
     */
    public function testVectorMixerResultsCount(): void {
        $arr1 = range(1, 4);
        $arr2 = range(1, 5);
        $mixer = new VectorMixer($arr1, $arr2);
        $countResults = 0;

        while ($mixer->isValidPattern()) {
            $mixer->next();
            $countResults++;
        }

        self::assertSame(126, $countResults); // 126 = (4 + 5)! / (4! * 5!)
    }

    /**
     * Проверим несколько результатов замешивания с большими векторами.
     */
    public function testLargeArraysFirstResult(): void {
        $arr1 = range(10000, 20000);
        $arr2 = range(50000, 60000);

        $mixer = new VectorMixer($arr1, $arr2);

        $expectedResult = array_merge(range(10000, 20000), range(50000, 60000));
        self::assertSame($expectedResult, $mixer->current());

        $mixer->next();
        $expectedResult = array_merge(range(10000, 19999), [50000], [20000], range(50001, 60000));
        self::assertSame($expectedResult, $mixer->current());

        $mixer->next();
        $expectedResult = array_merge(range(10000, 19999), [50000, 50001], [20000], range(50002, 60000));
        self::assertSame($expectedResult, $mixer->current());
    }
}
