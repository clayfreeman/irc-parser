<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Defines an interface used to describe & validate a byte class.
 */
interface ByteClassInterface {

  /**
   * Check if any disallowed bytes appear in the supplied input.
   *
   * @param string $input
   *   The input for which to check validity.
   *
   * @return bool
   *   TRUE if the input is valid for this class, FALSE otherwise.
   */
  public function validate(string $input): bool;

  /**
   * Gets the string value of the byte class.
   *
   * @return string
   *   The string value of the byte class.
   */
  public function value(): string;

}
