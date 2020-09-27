<?php

namespace ssp\module;

Class Datemod
{
    static function dateNoWeekends($date)
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $date);

        switch ($dt->format('N')) {
            case '6':
                $dt->sub(new \DateInterval('P1D'));
                break;
            case '7':
                $dt->add(new \DateInterval('P1D'));
                break;
        }

        return $dt->format('Y-m-d');
    }
}

