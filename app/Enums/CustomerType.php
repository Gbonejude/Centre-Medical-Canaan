<?php

namespace App\Enums;

enum CustomerType: string
{
    case HOME_CARE_CLIENTS = 'home_care_clients';
    case RESIDENTIAL_CLIENTS = 'residential_clients';
    case PRIVATE_CLIENTS = 'private_clients';
    case DD_WAIVER_CLIENTS = 'dd_waiver_clients';
    case IN_HOME_CLIENTS = 'in_home_clients';
    case DAY_PROGRAM_CLIENTS = 'day_program_clients';
}
