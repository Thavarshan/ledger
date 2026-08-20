<?php

use App\Models\ApiIdempotencyKey;
use Illuminate\Console\Scheduling\Schedule;

app(Schedule::class)
    ->command('model:prune', ['--model' => ApiIdempotencyKey::class])
    ->daily();
