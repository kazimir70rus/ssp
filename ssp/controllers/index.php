<?php

echo 'ok';

$msg = new \ssp\module\SessionVar('msg', 'ok');

echo $msg->getValue();

$msg->setValue('test');

echo $msg->popValue();
echo $msg->popValue();

