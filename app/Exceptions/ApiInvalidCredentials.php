<?php

namespace App\Exceptions;

use RuntimeException;

/** Indicates that API login credentials could not be authenticated. */
final class ApiInvalidCredentials extends RuntimeException {}
