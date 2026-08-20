<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Indicates that valid credentials require a second authentication factor.
 *
 * API clients should retry the login request with a valid TOTP or recovery code.
 */
final class ApiTwoFactorRequired extends RuntimeException {}
