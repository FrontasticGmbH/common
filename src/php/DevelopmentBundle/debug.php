<?php

function debug(... $args)
{
    \Frontastic\Common\DevelopmentBundle\Debugger::log(... $args);
}
