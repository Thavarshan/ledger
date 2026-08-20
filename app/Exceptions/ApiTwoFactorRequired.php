<?php

namespace App\Exceptions;

use RuntimeException;

/** Indicates that valid credentials require a second authentication factor. */
final class ApiTwoFactorRequired extends RuntimeException {}
