<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 10/04/2018
 * Time: 14:09
 */

namespace App\Util;


use PHPUnit\Framework\TestCase;


class CalculatorTest extends TestCase
{

    public function testAdd()
    {
        $calculator=new Calculator();
        $result =$calculator->add(2,2);
        // assert that your calculator added the numbers correctly!
        //if using symfony package
        //run ./vendor/bin/simple-phpunit
        //config file is in  phpunit.xml.dist file.

        //if using JC's
        //run  ./vendor/bin/phpunit tests
        $this->assertEquals(3, $result);
    }
}
