<?php

namespace App\Exceptions;

use RuntimeException;

/** Indicates that a supplied TOTP or recovery code is invalid. */
final class ApiInvalidTwoFactorCode extends RuntimeException {}
