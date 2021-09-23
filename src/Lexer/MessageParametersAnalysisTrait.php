<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Lexer;

use ClayFreeman\IRCParser\Lexeme;
use ClayFreeman\IRCParser\Token;

use Psr\Http\Message\StreamInterface;

/**
 * A trait used to perform lexical analysis on message parameters.
 */
trait MessageParametersAnalysisTrait {

  use BaseLexerTrait;

  /**
   * Generate a sequence of lexemes representing optional command parameters.
   *
   * If no parameters are present, this method won't generate any lexemes.
   *
   * Parameters are delimited by a 0x20 byte. A parameter byte prefix ':' may be
   * used to signify that the remainder of the input stream should be used as
   * the parameter value.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing command parameters.
   */
  protected function analyzeParameters(StreamInterface $input): \Generator {
    while (strlen($this->read($input, ' ')) > 0 && strlen($this->peek($input)) === 1) {
      $delimiters = [' '];

      if ($this->peek($input) === ':') {
        $this->discard($input);
        $delimiters = [];
      }

      $result = $this->readUntil($input, $delimiters);
      yield new Lexeme(Token::Parameter, $result);
    }
  }

}
