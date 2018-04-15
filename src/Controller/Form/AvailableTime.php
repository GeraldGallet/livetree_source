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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation;
use App\Controller\CustomApi;


class AvailableTime
extends Controller
{
    public const TIMEDIVISION_HOURS = 24;
    public const TIMEDIVISION_HALF_HOURS = 24 * 2;
    public const TIMEDIVISION_QUARTER_OF_HOUR = 24 * 4;
    public const TIMEDIVISION_MINUTES = 3600;
    private const TIMEDIVISION_str_hours = 'P0Y0DT1H0M';
    private const TIMEDIVISION_str_half_hours = 'P0Y0DT1H30M';
    private const TIMEDIVISION_str_quarter = 'P0Y0DT0H15M';
    private const TIMEDIVISION_str_minutes = 'P0Y0DT1H1M';

    public static $listedesreservation;

    /**
     * @Annotation\Route("/available")
     */
    function main()
    {
        $api_interface = new CustomApi();
        $bornes = $api_interface->table_get_all('resa_borne');
        $CRENNEAU = AvailableTime::init_tab(static::TIMEDIVISION_HOURS);
        $updated = AvailableTime::compare_disponibilities($bornes, $CRENNEAU);
//        dump($updated);
        return $this->render('reservations/reservation_bornes_tab.html.twig', array(
            'updated_list_of_slot'=> $updated));

    }


    /**
     * @param $timeDivision
     * @return  initiatedTab
     * @throws \Exception
     */
    static function init_tab($timeDivision)
    {
        $tmpStartingTimeOfTheDay = new DateTime('2018-12-12T00:00:00');
        switch ($timeDivision) {
            case AvailableTime::TIMEDIVISION_HOURS:
                $intervalToAdd = AvailableTime::TIMEDIVISION_str_hours;
                break;
            case AvailableTime::TIMEDIVISION_HALF_HOURS:
                $intervalToAdd = AvailableTime::TIMEDIVISION_str_half_hours;
                break;
            case AvailableTime::TIMEDIVISION_QUARTER_OF_HOUR:
                $intervalToAdd = AvailableTime::TIMEDIVISION_str_quarter;
                break;
            case AvailableTime::TIMEDIVISION_MINUTES:
                $intervalToAdd = AvailableTime::TIMEDIVISION_str_minutes;
                break;
            default:
                $intervalToAdd = AvailableTime::TIMEDIVISION_str_hours;
        }
        $CRENEAU = array();
        for ($i = 1; $i <= $timeDivision; $i++) {
            $tmp = clone $tmpStartingTimeOfTheDay;
            $CRENEAU[] = array('date' => $tmp, 'numberOfDisponibility' => 0);
            //add
            $tmpStartingTimeOfTheDay->add(new DateInterval($intervalToAdd));
        }
        return $CRENEAU;
    }

    /**
     * @param $dockList List charging docklist reservation
     * @param $slotAllocation time and number
     * @return $slotAllocation return le time and number
     */
    static function compare_disponibilities($dockList, $slotAllocation)
    {
        foreach ($dockList as $tuple) {
            $max = new DateTime($tuple['end_date']);
            $min = new DateTime($tuple['start_date']);
            foreach ($slotAllocation as &$timeNdisp) {
                //is inside?
                if ($min <= $timeNdisp['date'] && $max >= $timeNdisp['date']) {

                    $timeNdisp['numberOfDisponibility'] += 1;
//                    dump($timeNdisp['numberOfDisponibility']);
                }
            }
        }
        return $slotAllocation;
    }

}