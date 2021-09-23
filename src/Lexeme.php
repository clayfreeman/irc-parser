<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Represents a single IRCv3 lexeme.
 */
class Lexeme {

  /**
   * The value represented by the lexeme.
   *
   * @var mixed
   */
  public readonly mixed $value;

  /**
   * Constructs a Lexeme object.
   *
   * @param \ClayFreeman\IRCParser\Token $token
   *   The token of which the supplied value is an instance.
   * @param mixed $value
   *   The value represented by the lexeme.
   */
  public function __construct(public readonly Token $token, $value = NULL) {
    $this->value = $token->convert($value);
  }

}
