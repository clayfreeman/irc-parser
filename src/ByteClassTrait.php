<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Common methods used to implement a byte class.
 */
trait ByteClassTrait {

  /**
   * The value of the composite byte class.
   *
   * @var string
   */
  public readonly string $value;

  /**
   * {@inheritdoc}
   */
  public function validate(string $input): bool {
    return count(array_diff(str_split($input), str_split($this->value))) === 0;
  }

  /**
   * {@inheritdoc}
   */
  public function value(): string {
    return $this->value;
  }

}
