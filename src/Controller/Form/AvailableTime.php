<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 12/04/2018
 * Time: 14:59
 */

namespace App\Controller\Form;

use App\QueryConst;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation;
use App\Controller\CustomApi;


class AvailableTime
    extends Controller
{
    public const TIMEDIVISION_HOURS = 24;
    public const TIMEDIVISION_HALF_HOURS = 24 * 2;
    public const TIMEDIVISION_QUARTER_OF_HOUR = 24 * 4;
    public const TIMEDIVISION_MINUTES = 24 * 4 * 3;
    private const TIMEDIVISION_str_day = 'P0Y1DT0H0M';
    private const TIMEDIVISION_str_hours = 'P0Y0DT1H0M';
    private const TIMEDIVISION_str_half_hours = 'P0Y0DT1H30M';
    private const TIMEDIVISION_str_quarter = 'P0Y0DT0H15M';
    private const TIMEDIVISION_str_minutes = 'P0Y0DT0H5M';

    public static $listedesreservation;
    private static $numberMaxOfReservtion = null;
    private static $reservationAllowed = false;

    /**
     * @Annotation\Route("/available")
     */
    function main()
    {
        $reservation = array('end_date' => new DateTime('2018-12-16T12:00:00'), 'start_date' => new DateTime('2018-12-12T00:00:00'));
        $updated = array('numberMaxOfBookingPerParking' => null, 'updatedListeOfBooking' => null);
        $php_errormsg = null;
        try {
            $updated = AvailableTime::get_timeslots_with_placeId(1, $reservation);
//            dump($updated);
            $updated = AvailableTime::humanize_arrays($updated);
//            dump($updated);
        } catch (\Exception $exception) {
            $php_errormsg = $exception->getMessage();
            dump($php_errormsg);
        }
        return $this->render('reservations/reservation_bornes_tab.html.twig', array(
            'array_of_updated_list_of_slot' => $updated,
            'php_errormsg' => $php_errormsg
        ));
    }


    /**
     * Initiates a table with customisable time division and a startDate
     *
     * @param startDate
     * @param $intTimeDivision
     * @return  initiatedTab
     * @throws \Exception
     */
    static function init_tab($intTimeDivision, $startDate)
    {

        $tmpStartingTimeOfTheDay = clone $startDate;
        switch ($intTimeDivision) {
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
//                $intervalToAdd = AvailableTime::TIMEDIVISION_str_hours;
                throw new \Exception("init_tab-> intTimeDivision is not from the list");
        }
        $slotAllocation = array();

        for ($i = 1; $i <= $intTimeDivision; $i++) {
            $tmp = clone $tmpStartingTimeOfTheDay;
            $slotAllocation[] = array('date' => $tmp, 'numberOfDisponibility' => 0);
            $tmpStartingTimeOfTheDay->add(new DateInterval($intervalToAdd));
        }

        return $slotAllocation;
    }

    /**
     * Compares all the lists
     * @throws \Exception
     * @param $dockReservationList "List charging dockReservationList reservation"
     * @param $slotAllocation "time and number"
     * @param null $triggerexception
     * @return $slotAllocation return le time and number
     */
    static function compare_disponibilities($dockReservationList, &$slotAllocation, $numberMaxtriggerexception = null)
    {
        if (isset($dockReservationList)) {
            foreach ($dockReservationList as $tuple) {
                $max = new DateTime($tuple['end_date']);
                $min = new DateTime($tuple['start_date']);
                foreach ($slotAllocation as &$timeNdisp) {
                    //is inside?
                    if ($min <= $timeNdisp['date'] && $max >= $timeNdisp['date']) {
                        $timeNdisp['numberOfDisponibility'] += 1;
                        AvailableTime::$reservationAllowed = true;
                        if (!isset($numberMaxtriggerexception['numberMax'])
                            || $timeNdisp['numberOfDisponibility'] = $numberMaxtriggerexception['numberMax']) {
                            AvailableTime::$reservationAllowed = false;
                        }
                        if (!isset($numberMaxtriggerexception['numberMax'])
                            || $timeNdisp['numberOfDisponibility'] > $numberMaxtriggerexception['numberMax']) {
                            throw new Exception("Borne complète: nombre actuel ->(" . $timeNdisp['numberOfDisponibility'] . ") nombre disponible->(" . $numberMaxtriggerexception['numberMax'] . ")");
                        }
//                    dump($timeNdisp['numberOfDisponibility']);
                    }
                }
            }
        } else {
            throw new \Exception("DockReservationList isn't set. Check node app");
        }
        return $slotAllocation;
    }

    /**
     * @param $dockReservationList
     * @param $arrayOfSlotAllocation
     * @return mixed
     * @throws \Exception
     */
    static function array_compare_disponibilities($dockReservationList, &$arrayOfSlotAllocation)
    {

        foreach ($arrayOfSlotAllocation as &$slotAllocation) {
            try {
                self::compare_disponibilities($dockReservationList, $slotAllocation, AvailableTime::$numberMaxOfReservtion);
            } catch (Exception $exception) {
                throw $exception;

            }
        }

        return $arrayOfSlotAllocation;
    }

    /**
     * @param $dockReservationList
     * @param $targetedResa
     * @return mixed
     * @throws \Exception
     */
    static function get_disponibilities_with_resa_N_targetedResa($dockReservationList, $targetedResa)
    {
        $arrayOfSlotAllocation = AvailableTime::init_full_tab($targetedResa);
        try {
            $updated = AvailableTime::array_compare_disponibilities($dockReservationList, $arrayOfSlotAllocation);
        } catch (\Exception $exception) {
            throw $exception;
        }
        return $updated;
    }

    /**
     * @param $reservationEnQuestion
     * @return mixed
     */
    function round_dates($reservationEnQuestion)
    {
        $rouded = clone $reservationEnQuestion;
        $rouded->setTime(0, 0, 0);
        return $rouded;
    }

    /**
     * @param $rounded_start
     * @param $rounded_end
     * @return mixed
     */
    static function get_number_of_days($rounded_start, $rounded_end)
    {
//        dump($rounded_start,$rounded_end);
        $tmp = ($rounded_start->diff($rounded_end));
//        dump($tmp);
        $tmp = $tmp->format('%d');
//        dump($tmp);
        return $tmp;
    }

    /**
     * @param $reservationEnQuestion
     * @return array
     * @throws \Exception
     */
    static function init_full_tab($reservationEnQuestion)
    {
        $max = ($reservationEnQuestion['end_date']);
        $min = ($reservationEnQuestion['start_date']);
        if ($max < $min) {
            throw new \Exception("end_date is before start_date");
        }
        $roundedMax = self::round_dates($max);

        $roundedMin = self::round_dates($min);
        $number = intval(self::get_number_of_days($roundedMin, $roundedMax));
//        dump($max,$min,$number);
        $arrayOfSlotAllocation = array();
        for ($i = 1; $i <= $number + 1; $i++) {
            $arrayOfSlotAllocation[] = self::init_tab(self::TIMEDIVISION_MINUTES, $roundedMin);
            $roundedMin->add(new DateInterval(self::TIMEDIVISION_str_day));
//            dump($roundedMin);
        }

        return $arrayOfSlotAllocation;
    }

    /**
     * @param $id_place
     * @param $bookingDates
     * @return mixed
     */
    static function get_timeslots_with_placeId($id_place, $bookingDates, $reservationAllowed = null)
    {
        $updated = null;
        $updated['reservationAllowed'] = false;
        try {
            $updated = array('numberMaxOfBookingPerParking' => null, 'updatedListeOfBooking' => null);
            AvailableTime::$numberMaxOfReservtion['numberMax'] = self::get_number_of_docks($id_place);
            $updated['numberMaxOfBookingPerParking'] = AvailableTime::$numberMaxOfReservtion['numberMax'];
            $api_interface = new CustomApi();
            $bornes = $api_interface->table_get(
                'resa_borne',
                array('id_place' => strval($id_place)
                ));

            $updated['updatedListeOfBooking'] = AvailableTime::get_disponibilities_with_resa_N_targetedResa($bornes, $bookingDates);
            $updated['reservationAllowed'] = AvailableTime::$reservationAllowed;
            if (isset($reservationAllowed) && $reservationAllowed == true) {
                throw new \Exception("Borne complète");
            }
        } catch (\Exception $exception) {
            throw $exception;
        } finally {
            AvailableTime::$numberMaxOfReservtion = null;
        }
        return $updated;
    }

    static function get_number_of_docks($id_place)
    {
        if (isset($id_place)) {
            $api_interface = new CustomApi();
            $result = $api_interface->table_get("borne", array('id_place' => $id_place));
//            dump($result);
            if (!isset($result) || empty($result)) {
                throw new \Exception("Le nombre de places disponibles est inaccessible: check node app");
            } else {
                return sizeof($result);
            }
        }
        return null;
    }

    //===Affichage

    static function humanize_arrays_by_day($updated, $dayIndex, &$humanize)
    {
        $max = sizeof($updated['updatedListeOfBooking'][$dayIndex]);
        //dump($updated);
//        dump("max", $max);

        if (isset($updated['numberMaxOfBookingPerParking'])
            && isset($updated['updatedListeOfBooking'])
            && isset($updated['updatedListeOfBooking'][$dayIndex][0])) {
            //init humanize
            $humanize['numberMaxOfBookingPerParking'] = $updated['numberMaxOfBookingPerParking'];


            // init maxReached
            $numberMaxOfBookingPerParking = $updated['numberMaxOfBookingPerParking'];
            $numberOfDisponibility = $updated['updatedListeOfBooking'][$dayIndex][0]['numberOfDisponibility'];
            $maxReached = ($numberOfDisponibility >= $numberMaxOfBookingPerParking) ? true : false;
            $tmpMaxReached = $maxReached;
            //init tmp
            $humanize['updatedListeOfBooking'][$dayIndex][] = $updated['updatedListeOfBooking'][$dayIndex][0];// On initialise le tableau
            $i = 1;
            while ($i < $max) {
                $numberOfDisponibility = $updated['updatedListeOfBooking'][$dayIndex][$i]['numberOfDisponibility'];
                $maxReached = ($numberOfDisponibility >= $numberMaxOfBookingPerParking) ? true : false;

                if (!($maxReached == $tmpMaxReached)) {
                    //On écoute les variations des disponibilités
                    $tmpMaxReached = !$tmpMaxReached;
                    $humanize['updatedListeOfBooking'][$dayIndex][] = $updated['updatedListeOfBooking'][$dayIndex][$i - 1];//etat X
                    $humanize['updatedListeOfBooking'][$dayIndex][] = $updated['updatedListeOfBooking'][$dayIndex][$i];//etat non X
                    //      dump($humanize);
                }
                $i++;
            }
            $humanize['updatedListeOfBooking'][$dayIndex][] = $updated['updatedListeOfBooking'][$dayIndex][$i - 1];// On cloture le tableau

        } else {
//            dump("yo");
            if (!isset($updated['numberMaxOfBookingPerParking'])) {
                throw new \Exception("number of maxBonking per parking isn't set 1");
            } elseif (!isset($updated['updatedListeOfBooking'][$dayIndex])) {
                throw new \Exception("The (" . $dayIndex . ")th day of reservation isn't set");
            } elseif (!isset($updated['updatedListeOfBooking'][$dayIndex][0])) {
                throw new \Exception("First time slot of the (" . $dayIndex . ")th day isnt set");
            } else {
                throw new \Exception("isnt set at all");
            }
        }
    }

    static function humanize_arrays($updated)
    {
        $humanize = array('numberMaxOfBookingPerParking' => null, 'updatedListeOfBooking' => null);
        $numberOfDay = sizeof($updated['updatedListeOfBooking']);
        $dayIndex = 0;
        try {
            while ($dayIndex < $numberOfDay) {
                self::humanize_arrays_by_day($updated, $dayIndex, $humanize);
                $dayIndex++;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
        return $humanize;
    }
}