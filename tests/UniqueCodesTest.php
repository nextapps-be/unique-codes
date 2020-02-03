<?php

namespace NextApps\UniqueCodes\Tests;

use Generator;
use NextApps\UniqueCodes\UniqueCodes;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UniqueCodesTest extends TestCase
{
    /** @test */
    public function it_returns_generator_by_default()
    {
        $codes = (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);

        $this->assertInstanceOf(Generator::class, $codes);
    }

    /** @test */
    public function it_returns_array_if_requested()
    {
        $codes = (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100, true);

        $this->assertIsArray($codes);
    }

    /** @test */
    public function it_generates_unique_codes()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->generate(1, 100)
        );

        $this->assertCount(100, $codes);
        $this->assertCount(100, array_unique($codes));
    }

    /** @test */
    public function it_generates_unique_codes_within_range()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->generate(25, 50)
        );

        $this->assertCount(50, $codes);
        $this->assertCount(50, array_unique($codes));
    }

    /** @test */
    public function it_generates_codes_without_duplicate_characters()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->generate(1, 100)
        );

        foreach ($codes as $code) {
            $this->assertEquals(6, strlen($code));
            $this->assertCount(6, array_unique(str_split($code)));
        }
    }

    /** @test */
    public function it_generates_codes_that_only_contain_characters_from_specified_character_list()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4 WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->generate(1, 100)
        );

        foreach ($codes as $code) {
            $this->assertEquals(6, strlen($code));
            $this->assertCount(6, array_unique(str_split($code)));
        }
    }

    /** @test */
    public function it_generates_codes_with_prefix()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->setPrefix('TEST')
                ->generate(1, 100)
        );

        $this->assertCount(100, array_unique($codes));

        foreach ($codes as $code) {
            $this->assertEquals(10, strlen($code));
            $this->assertEquals('TEST', substr($code, 0, 4));
        }
    }

    /** @test */
    public function it_generates_codes_with_suffix()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->setSuffix('TEST')
                ->generate(1, 100)
        );

        $this->assertCount(100, array_unique($codes));

        foreach ($codes as $code) {
            $this->assertEquals(10, strlen($code));
            $this->assertEquals('TEST', substr($code, 6, 4));
        }
    }

    /** @test */
    public function it_generates_codes_with_prefix_and_suffix()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->setPrefix('PREFIX')
                ->setSuffix('SUFFIX')
                ->generate(1, 100)
        );

        $this->assertCount(100, array_unique($codes));

        foreach ($codes as $code) {
            $this->assertEquals(18, strlen($code));
            $this->assertEquals('PREFIX', substr($code, 0, 6));
            $this->assertEquals('SUFFIX', substr($code, 12, 6));
        }
    }

    /** @test */
    public function it_generates_codes_with_delimiter()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->setDelimiter('-', 3)
                ->generate(1, 100)
        );

        $this->assertCount(100, array_unique($codes));

        foreach ($codes as $code) {
            $this->assertEquals(7, strlen($code));
            $this->assertEquals('-', substr($code, 3, 1));
        }
    }

    /** @test */
    public function it_generates_codes_with_suffix_and_prefix_and_delimiter()
    {
        $codes = iterator_to_array(
            (new UniqueCodes())
                ->setPrime(17)
                ->setMaxPrime(101)
                ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
                ->setLength(6)
                ->setPrefix('PREFIX')
                ->setSuffix('SUFFIX')
                ->setDelimiter('-', 3)
                ->generate(1, 100)
        );

        $this->assertCount(100, array_unique($codes));

        foreach ($codes as $code) {
            $this->assertEquals(21, strlen($code));
            $this->assertEquals('PREFIX-', substr($code, 0, 7));
            $this->assertEquals('-', substr($code, 10, 1));
            $this->assertEquals('-SUFFIX', substr($code, 14, 7));
        }
    }

    /** @test */
    public function it_throws_exception_if_prime_is_not_specified()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Prime number must be specified');

        (new UniqueCodes())
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_max_prime_is_not_specified()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Max prime number must be specified');

        (new UniqueCodes())
            ->setPrime(17)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_prime_is_not_an_integer()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Prime number must be an integer');

        (new UniqueCodes())
            ->setPrime(17.1)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_max_prime_is_not_an_integer()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Max prime number must be an integer');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101.1)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_start_is_not_an_integer()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Start must be an integer');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1.1, 100);
    }

    /** @test */
    public function it_throws_exception_if_amount_is_not_an_integer()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Amount must be an integer');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100.1);
    }

    /** @test */
    public function it_throws_exception_if_characters_are_not_specified()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Character list must be specified');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_length_is_not_specified()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Length must be specified');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_prime_number_is_bigger_than_max_prime_number()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Prime number must be smaller than the max prime number');

        (new UniqueCodes())
            ->setPrime(101)
            ->setMaxPrime(17)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_prime_number_is_equal_to_max_prime_number()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Prime number must be smaller than the max prime number');

        (new UniqueCodes())
            ->setPrime(101)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_prime_number_is_not_prime()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Prime number must be prime');

        (new UniqueCodes())
            ->setPrime(52)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_max_prime_number_is_not_prime()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Max prime number must be prime');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(1001)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_size_of_character_list_is_smaller_than_specified_code_length()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The size of the character list must be bigger or equal to the length of the code');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCK')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_size_of_character_list_equals_specified_code_length()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The size of the character list must be bigger or equal to the length of the code');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZ')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_character_list_contains_duplicates()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The character list can not contain duplicates');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZL')
            ->setLength(6)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_max_prime_number_is_too_big_for_the_specified_character_list_and_code_length()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The length of the code is too short to create the number of unique codes equal to the max prime number');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJC')
            ->setLength(3)
            ->generate(1, 100);
    }

    /** @test */
    public function it_throws_exception_if_sum_of_start_and_amount_is_bigger_than_max_prime_number()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The number of codes you create can not be bigger or equal to the max prime number');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(50, 150);
    }

    /** @test */
    public function it_throws_exception_if_sum_of_start_and_amount_equals_max_prime_number()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The number of codes you create can not be bigger or equal to the max prime number');

        (new UniqueCodes())
            ->setPrime(17)
            ->setMaxPrime(101)
            ->setCharacters('LQJCKZM4WDPT69S7XRGANY23VBH58F1')
            ->setLength(6)
            ->generate(50, 52);
    }
}
