<?php

$guide = new \ssp\models\Guide($db);

\ssp\module\Tools::send_json($guide->getTypeResults($param[1]));

