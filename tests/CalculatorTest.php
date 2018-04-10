<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 10/04/2018
 * Time: 14:09
 */

namespace App\Util;


use function Sodium\add;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function testAdd()
    {
        $calculator=new Calculator();
        $result =$calculator->add(2,2);
        // assert that your calculator added the numbers correctly!
        //run ./vendor/bin/simple-phpunit
        //config file is in  phpunit.xml.dist file.
        $this->assertEquals(3, $result);
    }
}
