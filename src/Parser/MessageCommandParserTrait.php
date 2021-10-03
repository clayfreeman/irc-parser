<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser;

use ClayFreeman\IRCParser\LexemeStream;
use ClayFreeman\IRCParser\Token;

/**
 * A trait used to parse a message command lexeme.
 */
trait MessageCommandParserTrait {

  use BaseParserTrait;

  /**
   * Parse a command from the supplied stream.
   *
   * @param \ClayFreeman\IRCParser\LexemeStream $stream
   *   The lexeme stream from which to parse a command.
   *
   * @return int|string
   *   The parsed numeric or command.
   */
  protected function parseCommand(LexemeStream $stream): int|string {
    return $this->consume($stream, Token::Command)->value;
  }

}
