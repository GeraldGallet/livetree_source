<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 12/04/2018
 * Time: 14:59
 */

namespace App\Controller\Form;

use DateInterval;
use DateTime;
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
        $TimeDivision=24;
        $api_interface = new CustomApi();
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T17:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T17:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T17:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T20:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T20:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T20:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T21:03:01'));
        $listedesreservation[] = array('min' => new DateTime('2011-01-01T15:03:01'), 'max' => new DateTime('2011-01-01T23:03:01'));

        //init tab
        $startingTimeOfTheDay = new DateTime('2011-01-01T00:00:00');
        $array = array();
        $CRENNEAU = array();
        for ($i = 1; $i <= $TimeDivision; $i++) {
            $tmp= clone $startingTimeOfTheDay;
            $CRENNEAU[]=array('date'=>$tmp,'numberOfDisponibility'=>0);

            $startingTimeOfTheDay->add(new DateInterval('P0Y0DT1H0M'));
        }
        dump($CRENNEAU);
        foreach ($listedesreservation as $tuple) {
            $max = $tuple['max'];
            $min = $tuple['min'];

            foreach ($CRENNEAU as &$timeNdisp){
                if ($min <= $timeNdisp['date'] && $max >= $timeNdisp['date']) {
                    $mmtemp =$timeNdisp['numberOfDisponibility'];
                    $mmtemp+=1;
                    $timeNdisp['numberOfDisponibility']=$mmtemp;
                    dump($timeNdisp['numberOfDisponibility'],$mmtemp);
                    /*
                    dump($min <= $timeNdisp['date'] && $max >= $timeNdisp['date']);*/

                }
            }
        }
        dump($listedesreservation);
        dump($CRENNEAU);
        //$bornes = $api_interface->reservation_borne_get_all();
        //dump($bornes);

        $datetime1 = new DateTime('2009-10-11');
        $datetime2 = new DateTime('2009-10-13');
        $interval = $datetime1->diff($datetime2);
        echo $interval->format('%R%a days');
        return new JsonResponse($array);
    }

}