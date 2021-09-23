<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser;

use ClayFreeman\IRCParser\LexemeStream;
use ClayFreeman\IRCParser\Token;

/**
 * A trait used to parse a sequence of message parameter lexemes.
 */
trait MessageParametersParserTrait {

  use BaseParserTrait;

  /**
   * Attempt to parse an optional parameter list from the supplied stream.
   *
   * @param \ClayFreeman\IRCParser\LexemeStream $stream
   *   The lexeme stream from which to parse an optional parameter list.
   *
   * @return string[]
   *   A list of parameters parsed from the lexeme stream.
   */
  protected function parseParameters(LexemeStream $stream): array {
    $parameters = [];

    while ($stream->peek()?->token === Token::Parameter) {
      $parameters[] = $stream->consume()->value;
    }

    return $parameters;
  }

}
