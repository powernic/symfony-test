<?php


namespace App\Utils;

use DateTime;

class Validator
{

    public function validateTitle($title)
    {
        if (empty($title)) {
            throw new \Exception('The title can not be empty.');
        }

        return $title;
    }

    public function validateDescription($descriprion)
    {
        if (empty($descriprion)) {
            throw new \Exception('The description can not be empty.');
        }

        return $descriprion;
    }


    public function validateDate($date)
    {
        $d1 = strtotime($date);

        if (empty($date)) {
            throw new \Exception('The date can not be empty.');
        }
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!($d && $d->format('Y-m-d') === $date)){
            throw new \Exception('Is invalid date format. (Y-m-d)' );
        }
        return $date;
    }


}
