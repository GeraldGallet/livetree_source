<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 18/04/2018
 * Time: 16:41
 */

namespace App;


class QueryConst
{

    /**
     * get all resa where parking = x
     */
    public static function sqlAll_resa_by_parking($id_place){
        return "WHERE resa_borne.id_place=". strval($id_place);
    }
}