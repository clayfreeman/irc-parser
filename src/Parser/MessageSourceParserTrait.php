<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser;

use ClayFreeman\IRCParser\LexemeStream;
use ClayFreeman\IRCParser\Token;
use ClayFreeman\IRCParser\Parser\Value\Source;

/**
 * A trait used to parse a sequence of message source lexemes.
 */
trait MessageSourceParserTrait {

  use BaseParserTrait;

  /**
   * Attempt to parse a message source from the supplied stream.
   *
   * If no message source is present, this method will produce NULL.
   *
   * @param \ClayFreeman\IRCParser\LexemeStream $stream
   *   The lexeme stream from which to parse an optional message source.
   *
   * @return \ClayFreeman\IRCParser\Parser\Value\Source|null
   *   A message source parsed from the lexeme stream, or NULL.
   */
  protected function parseSource(LexemeStream $stream): ?Source {
    $source = NULL;

    if ($stream->peek()?->token === Token::Source) {
      $source = new Source($stream->consume()->value);
    }

    // Define the optional follow-up tokens that may appear.
    $tokens = [Token::SourceUser, Token::SourceHost];

    // Determine whether an optional follow-up token appears next in the stream.
    if ($source && in_array($stream->peek()?->token, $tokens, TRUE)) {
      $lexeme = $stream->consume();
      $source->host = $lexeme->value;

      // If a user token was consumed, it must be followed by a host token.
      if ($lexeme->token === Token::SourceUser) {
        $source->host = $this->consume($stream, Token::SourceHost)->value;
        $source->user = $lexeme->value;
      }
    }

    return $source;
  }

}
