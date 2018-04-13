<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 12/04/2018
 * Time: 14:59
 */

namespace App\Controller\Form;

use App\Controller\jc\Pair;
use Symfony\Component\Routing\Annotation;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\CustomApi;
class AvailableTime
{
    public static $listedesreservation;

    /**
     * @Annotation\Route("/available")
     */
    function aa()
    {
        $api_interface = new CustomApi();

        $listedesreservation[]= array('min'=>2,'max'=> 4);
        $listedesreservation[] = array('min'=>2,'max'=> 4);
        $listedesreservation[] = array('min'=>2,'max'=> 4);
        $listedesreservation[] = array('min'=>2,'max'=> 5);
        $listedesreservation[] = array('min'=>2,'max'=> 5);
        $listedesreservation[] = array('min'=>2,'max'=> 5);
        $listedesreservation[] = array('min'=>2,'max'=> 7);
        $listedesreservation[] = array('min'=>2,'max'=> 10);


        $array = array();
        for ($i = 1; $i <= 11; $i++) {
            $array[] = 0;
        }
        dump($array);
        foreach ($listedesreservation as $tuple) {
            $max = $tuple['max'];
            $min = $tuple['min'];

            for ($i = 1; $i <= 10; $i++) {
                if ($min <= $i && $max >= $i) {
                    $array[$i] +=1;
                }
            }
        }
        dump($array);

        $bornes=$api_interface->reservation_borne_get_all();
        dump($bornes);
        return new JsonResponse($array);
    }

}