<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Indicates that API login credentials could not be authenticated.
 *
 * The API maps this exception to a generic authentication failure response.
 */
final class ApiInvalidCredentials extends RuntimeException {}
