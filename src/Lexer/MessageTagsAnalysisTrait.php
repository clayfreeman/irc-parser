<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Lexer;

use ClayFreeman\IRCParser\Lexeme;
use ClayFreeman\IRCParser\Token;

use Psr\Http\Message\StreamInterface;

/**
 * A trait used to perform lexical analysis on a message tag list.
 */
trait MessageTagsAnalysisTrait {

  use BaseLexerTrait;

  /**
   * Generate a sequence of lexemes representing a single tag.
   *
   * Each tag consists of at least a name, but may be optionally preceded by a
   * client prefix and a vendor, and may be optionally followed by a value.
   *
   * If no tag value is specified, the value should be assumed to be TRUE.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing a single tag.
   */
  private function analyzeTag(StreamInterface $input): \Generator {
    foreach ($this->analyzeTagName($input) as $lexeme) {
      yield $lexeme;
    }

    // Determine whether to generate a lexeme for a tag value.
    if ($this->peek($input) === '=') {
      foreach ($this->analyzeTagValue($input) as $lexeme) {
        yield $lexeme;
      }
    }
  }

  /**
   * Generate a sequence of lexemes for a tag name.
   *
   * Tag names are optionally prefixed by a client prefix, a vendor, or both
   * (only in the listed order).
   *
   * If a vendor is specified, a more specific tag name token will follow.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing a tag name.
   */
  private function analyzeTagName(StreamInterface $input): \Generator {
    // Check for the presence of the client prefix on this tag's name.
    if ($this->peek($input) === '+') {
      yield new Lexeme(Token::TagPrefixClient);
      $this->discard($input);
    }

    // Read until the next delimiter:
    //
    // - end of tag list delimiter (byte 0x20).
    // - name delimiter (byte '/').
    // - end of tag delimiter (byte ';').
    // - value delimiter (byte '=').
    $result = $this->readUntil($input, ' /;=', 'expecting tag vendor or tag name');
    yield new Lexeme(Token::Tag, $result);

    // Check if the next byte in the input stream is the tag name delimiter.
    if ($this->peek($input) === '/') {
      $this->discard($input);

      // Read until the next delimiter:
      //
      // - end of tag list delimiter (byte 0x20).
      // - end of tag delimiter (byte ';').
      // - value delimiter (byte '=').
      $result = $this->readUntil($input, ' ;=', 'expecting tag name');
      yield new Lexeme(Token::TagName, $result);
    }
  }

  /**
   * Generate a lexeme for a tag value.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A lexeme representing a tag value.
   */
  private function analyzeTagValue(StreamInterface $input): \Generator {
    $this->discard($input);

    // Read until the next delimiter:
    //
    // - NUL (byte 0x00)
    // - end of tag list delimiter (byte 0x20).
    // - end of tag delimiter (byte ';').
    $result = $this->readUntil($input, "\0 ;");
    yield new Lexeme(Token::TagValue, $result);
  }

  /**
   * Generate a sequence of lexemes for an optional tag list.
   *
   * If no tag list is present, this method won't generate any lexemes.
   *
   * Tags are read continuously until the end of the tag list is encountered
   * (delimited by a 0x20 byte). Successive tags are separated by a semicolon.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing a tag list.
   */
  protected function analyzeTags(StreamInterface $input): \Generator {
    if ($this->peek($input) === '@') {
      yield from $this->analyzeTagsPresent($input);
    }

    yield from [];
  }

  /**
   * Generate a sequence of lexemes for a tag list.
   *
   * This method expects that the presence of a tag list has been established
   * prior to being invoked. Tag lists are prefixed by the '@' byte.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes representing a tag list.
   */
  private function analyzeTagsPresent(StreamInterface $input): \Generator {
    // Loop continuously until there are no more tags to process.
    do {
      // Consume either a tag list start delimiter ('@'; the initial case), or
      // the delimiter used to separate an additional tag (';').
      $this->discard($input);

      // Attempt to process a tag in the input stream, yielding its lexemes.
      foreach ($this->analyzeTag($input) as $lexeme) {
        yield $lexeme;
      }
    } while ($this->peek($input) === ';');

    // If all tags have been processed, consume the end of tag list delimiter.
    $this->consumeSpace($input, 'expecting end of tag list');
  }

}
