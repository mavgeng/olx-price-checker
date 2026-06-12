<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('adverts:check')
    ->everyFiveMinutes()
    ->withoutOverlapping();
