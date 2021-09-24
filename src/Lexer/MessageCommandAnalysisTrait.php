<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Lexer;

use ClayFreeman\IRCParser\Lexeme;
use ClayFreeman\IRCParser\Token;

use Psr\Http\Message\StreamInterface;

/**
 * A trait used to perform lexical analysis on a message command.
 */
trait MessageCommandAnalysisTrait {

  use BaseLexerTrait;

  /**
   * Generate a lexeme representing the message command.
   *
   * The message command, as defined by the RFCs, is either a three-digit
   * numeric or an alpha string. This method provides no checking against the
   * specification, and simply reads until the next 0x20 byte or EOF.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A lexeme representing the message command.
   */
  protected function analyzeCommand(StreamInterface $input): \Generator {
    $result = $this->readUntil($input, ' ', 'expecting command or numeric');
    yield new Lexeme(Token::Command, $result);
  }

}
