<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Indicates that a supplied TOTP or recovery code is invalid.
 *
 * The exception intentionally does not reveal which second-factor value failed.
 */
final class ApiInvalidTwoFactorCode extends RuntimeException {}
