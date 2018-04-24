<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 12/04/2018
 * Time: 14:59
 */

namespace App\Controller\Form;

use App\Controller\CustomApi;
use DateTime;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation;


class AvailableTimeCar
    extends AvailableTime
{


    private $numberMaxOfReservtion = null;
    private $reservationAllowed = false;

    /**
     * @return null
     */
    public function getNumberMaxOfReservtion()
    {
        return $this->numberMaxOfReservtion;
    }

    /**
     * @param null $numberMaxOfReservtion
     */
    public function setNumberMaxOfReservtion($numberMaxOfReservtion): void
    {
        $this->numberMaxOfReservtion = $numberMaxOfReservtion;
    }

    /**
     * @return bool
     */
    public function isReservationAllowed(): bool
    {
        return $this->reservationAllowed;
    }

    /**
     * @param bool $reservationAllowed
     */
    public function setReservationAllowed(bool $reservationAllowed): void
    {
        $this->reservationAllowed = $reservationAllowed;
    }
//======================================================================================================================

    /**
     * @Annotation\Route("/available/car")
     */
    function main()
    {
        $reservation = array('end_date' => new DateTime('2018-04-30T12:00:00'), 'start_date' => new DateTime('2018-04-29T00:00:00'));
        $updated = array('numberMaxOfBookingPerParking' => null, 'updatedListeOfBooking' => null);
        $php_errormsg = null;
        $available = new AvailableTimeCar();
        try {
            $id = 1;
            $updated = $available->get_timeslots_with_CarID($id, $reservation
//                , true
            );
            $updated = $available->humanize_arrays($updated);//humanize_arrays($updated);
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
     * Compares all the lists
     * @throws \Exception
     * @param $dockReservationList "List charging dockReservationList reservation"
     * @param $slotAllocation "time and number"
     * @param null $triggerexception
     * @return $slotAllocation return le time and number
     */
    public function compare_disponibilities($dockReservationList, &$slotAllocation, $numberMaxtriggerexception = null)
    {
        if (isset($dockReservationList)) {
            foreach ($dockReservationList as $tuple) {
                $max = new DateTime($tuple['end_date']);
                $min = new DateTime($tuple['start_date']);
                foreach ($slotAllocation as &$timeNdisp) {
                    //is inside?
                    if ($min <= $timeNdisp['date'] && $max >= $timeNdisp['date']) {
                        $timeNdisp['numberOfDisponibility'] += 1;
                        if (!isset($numberMaxtriggerexception['numberMax'])
                            || $timeNdisp['numberOfDisponibility'] == $numberMaxtriggerexception['numberMax']) {
                            $this->setReservationAllowed(false);
                            //throw new Exception("Borne complète: nombre actuel ->(" . $timeNdisp['numberOfDisponibility'] . ") nombre disponible->(" . $numberMaxtriggerexception['numberMax'] . ")");

                        }
                        if (!isset($numberMaxtriggerexception['numberMax'])
                            || $timeNdisp['numberOfDisponibility'] > $numberMaxtriggerexception['numberMax']) {
                            $this->setReservationAllowed(false);
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
    public function array_compare_disponibilities($dockReservationList, &$arrayOfSlotAllocation)
    {

        foreach ($arrayOfSlotAllocation as &$slotAllocation) {
            try {
                $this->compare_disponibilities($dockReservationList, $slotAllocation, $this->getNumberMaxOfReservtion());
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
    public function get_disponibilities_with_resa_N_targetedResa($dockReservationList, $targetedResa)
    {
        $arrayOfSlotAllocation = $this->init_full_tab($targetedResa);
        try {
            $updated = $this->array_compare_disponibilities($dockReservationList, $arrayOfSlotAllocation);
        } catch (\Exception $exception) {
            throw $exception;
        }
        return $updated;
    }





    /**
     * @param $id_company_car
     * @param $bookingDates
     * @return mixed
     */
    public function get_timeslots_with_CarId($id_car_company, $bookingDates, $triggerExceptionReservationAllowed = null)
    {
        $updated = null;
        $updated['reservationAllowed'] = false;
        $this->setReservationAllowed(true);

        try {
            $updated = array('numberMaxOfBookingPerParking' => null, 'updatedListeOfBooking' => null);
            $this->setNumberMaxOfReservtion(array('numberMax' => $this->get_car_Int($id_car_company)));
            $updated['numberMaxOfBookingPerParking'] = $this->getNumberMaxOfReservtion()['numberMax'];
            $resa_car = $this->get_resa_car($id_car_company);

            $updated['updatedListeOfBooking'] = $this->get_disponibilities_with_resa_N_targetedResa($resa_car, $bookingDates);
            $updated['reservationAllowed'] = $this->isReservationAllowed();
//            dump(isset($triggerExceptionReservationAllowed));
//            dump(($triggerExceptionReservationAllowed == true) );
//            dump(($this->isReservationAllowed())==false);
            if (isset($triggerExceptionReservationAllowed) && ($triggerExceptionReservationAllowed == true) && ($this->isReservationAllowed()) == false) {
                //$updated['reservationAllowed'] = false;
                throw new \Exception("Borne complète");
            }
        } catch (\Exception $exception) {
            $this->setReservationAllowed(false);
            $updated['reservationAllowed'] = false;
            throw $exception;
        } finally {
            $this->setNumberMaxOfReservtion(null);
        }
        return $updated;
    }



    public function get_car_Int($id_company_car)
    {
        if (isset($id_company_car)) {
            $api_interface = new CustomApi();
            $result = $api_interface->table_get("company_car", array('id_company_car' => $id_company_car));
//            dump($result);
            if (!isset($result) || empty($result)) {
                throw new \Exception("L'id de la voiture n'existe pas ou est inaccessible: check node app / Database");
            } else {
                return sizeof($result);
            }
        } else {
            throw new \Exception("id_company_car isn't set");
        }
    }
    public function get_resa_car($id_company_car)
    {
        if (isset($id_company_car)) {
            $api_interface = new CustomApi();
            $result = $api_interface->table_get("resa_car", array('id_company_car' => $id_company_car));
//            dump(!isset($result));
            if (!isset($result) /*|| empty($result)*/) {
                throw new \Exception("Les réservations de la voiture sont inaccessibles: check node app");
            } else {

                $configured = $this->configure_resa_car_like_resa_bornes($result);
                dump($configured);
                return $configured;
            }
        } else {
            throw new \Exception("id_company_car isn't set");

        }
    }

    private function configure_resa_car_like_resa_bornes($dockReservationList)
    {
        $result = array();
        if (isset($dockReservationList)) {
            foreach ($dockReservationList as $tuple) {
                //max
                $max = new DateTime($tuple['date_end']);
                $max2 = new DateTime( $tuple['end_time']);
                AvailableTimeCar::mySetTime($max,$max2);
                //min
                $min = new DateTime($tuple['date_start']);
                $min2=new DateTime($tuple['start_time']);
                AvailableTimeCar::mySetTime($min,$min2);


                $tmp = array('end_date' => $max, 'start_date' => $min);
                $result[] = $tmp;

                //dump("yo",$max,new DateTime($max),"date", $tuple['date_end'], "time", $tuple['end_time']);
            }
            return $result;
        } else {
            throw new \Exception("DockReservationList isn't set");
        }
    }
    private static function mySetTime(DateTime &$date, DateTime $time){
        $date->modify("+2 hour");
        dump($date);
        $date->setTime(
            intval($time->format('H')),
            intval($time->format('i')),
            intval($time->format('s'))
        );
        $date=$date->format('Y-m-d H:i:s');


    }



    //===Affichage

}
