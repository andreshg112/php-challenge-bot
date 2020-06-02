<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use App\Services\Amdoren\Currency;
use Illuminate\Support\Collection;
use App\Exceptions\InvalidCurrency;

class CurrencyTest extends TestCase
{
    /**
     * No error.
     *
     * @return void
     */
    public function testConvert()
    {
        $list = Currency::list();

        $from = $this->faker->randomElement($list->keys()->toArray());

        $to = $this->faker->randomElement(
            $list->except($from)->keys()->toArray()
        );

        $amount = $this->faker->randomNumber(3);

        $actual = Currency::convert($from, $to, $amount);

        $this->assertIsFloat($actual);
    }

    /**
     * Expected error because of invalid $from.
     *
     * @return void
     */
    public function testConvert1()
    {
        $this->expectException(InvalidCurrency::class);

        $this->expectExceptionCode(210);

        $list = Currency::list();

        $from = 'ABC';

        $to = $this->faker->randomElement($list->keys()->toArray());

        $amount = $this->faker->randomNumber(3);

        Currency::convert($from, $to, $amount);
    }

    /**
     * Expected error because of invalid $to.
     *
     * @return void
     */
    public function testConvert2()
    {
        $this->expectException(InvalidCurrency::class);

        $this->expectExceptionCode(260);

        $list = Currency::list();

        $from = $this->faker->randomElement($list->keys()->toArray());

        $to = 'ABC';

        $amount = $this->faker->randomNumber(3);

        Currency::convert($from, $to, $amount);
    }

    /**
     * Expected error because of invalid $amount.
     *
     * @return void
     */
    public function testConvert3()
    {
        $this->expectException(Exception::class);

        $this->expectExceptionCode(310);

        $list = Currency::list();

        $from = $this->faker->randomElement($list->keys()->toArray());

        $to = $this->faker->randomElement(
            $list->except($from)->keys()->toArray()
        );

        do {
            // It has to be greater than 100000000 to generate error.
            $amount = $this->faker->randomNumber(9);
        } while ($amount < 100000001);

        Currency::convert($from, $to, $amount);
    }

    /**
     * Expected error because of $amount = 0.
     *
     * @return void
     */
    public function testConvert4()
    {
        $this->expectException(Exception::class);

        $this->expectExceptionCode(320);

        $list = Currency::list();

        $from = $this->faker->randomElement($list->keys()->toArray());

        $to = $this->faker->randomElement(
            $list->except($from)->keys()->toArray()
        );

        $amount = 0;

        Currency::convert($from, $to, $amount);
    }

    public function testList()
    {
        $list = Currency::list();

        $this->assertInstanceOf(Collection::class, $list);

        $this->assertSame('Colombian Peso', $list->get('COP'));

        $this->assertSame('Euro', $list->get('EUR'));

        $this->assertSame('United States Dollar', $list->get('USD'));
    }
}
