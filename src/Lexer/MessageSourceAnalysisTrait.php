<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Lexer;

use ClayFreeman\IRCParser\Lexeme;
use ClayFreeman\IRCParser\Token;

use Psr\Http\Message\StreamInterface;

/**
 * A trait used to perform lexical analysis on a message source.
 */
trait MessageSourceAnalysisTrait {

  use BaseLexerTrait;

  /**
   * Generate a sequence of lexemes representing the message source.
   *
   * If no source is present, this method won't generate any lexemes.
   *
   * The principal of the source is ambiguous, and can either be a server
   * hostname or a client nickname. If the principal is followed by a client
   * user or hostname token, then it is safe to assume that the principal token
   * is a client nickname.
   *
   * In cases where the principal token is the only token produced by this
   * method, the source remains ambiguous until further meaning can be derived
   * in downstream code.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing the message source.
   */
  protected function analyzeSource(StreamInterface $input): \Generator {
    if ($this->peek($input) === ':') {
      yield from $this->analyzeSourcePresent($input);
    }

    yield from [];
  }

  /**
   * Generate a lexeme representing the message source host.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A lexeme representing a message source host.
   */
  private function analyzeSourceHost(StreamInterface $input): \Generator {
    $this->discard($input);

    // Read until the end of source delimiter (byte 0x20).
    $result = $this->readUntil($input, [' '], 'expecting source host');
    yield new Lexeme(Token::SourceHost, $result);
  }

  /**
   * Generate a sequence of lexemes representing the message source.
   *
   * This method expects that the presence of a source has been established
   * prior to being invoked. The message source is prefixed by the ':' byte.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing the message source.
   */
  private function analyzeSourcePresent(StreamInterface $input): \Generator {
    yield from $this->analyzeSourcePrincipal($input);

    if ($this->peek($input) === '!') {
      $lexeme_generator = $this->analyzeSourceUser($input);
    }
    elseif ($this->peek($input) === '@') {
      $lexeme_generator = $this->analyzeSourceHost($input);
    }

    foreach ($lexeme_generator ?? [] as $lexeme) {
      yield $lexeme;
    }

    // Consume the end of message source delimiter.
    $this->consumeSpace($input, 'expecting end of source');
  }

  /**
   * Generate a lexeme representing the message source principal.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A lexeme representing a message source principal.
   */
  private function analyzeSourcePrincipal(StreamInterface $input): \Generator {
    $this->discard($input);

    // Read until the next delimiter:
    //
    // - end of source delimiter (byte 0x20).
    // - user delimiter (byte '!').
    // - host delimiter (byte '@').
    $result = $this->readUntil($input, [' ', '!', '@'], 'expecting source');
    yield new Lexeme(Token::Source, $result);
  }

  /**
   * Generate a lexeme representing the message source user.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A lexeme representing a message source user.
   */
  private function analyzeSourceUser(StreamInterface $input): \Generator {
    $this->discard($input);

    // Read until the next delimiter:
    //
    // - end of source delimiter (byte 0x20).
    // - host delimiter (byte '@').
    $result = $this->readUntil($input, [' ', '@'], 'expecting source user');
    yield new Lexeme(Token::SourceUser, $result);

    // Check if the next byte in the input stream is the host delimiter.
    if ($this->peek($input) !== '@') {
      $this->error($input, 'expecting source host');
    }

    foreach ($this->analyzeSourceHost($input) as $lexeme) {
      yield $lexeme;
    }
  }

}
