<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

use ClayFreeman\IRCParser\Lexer\MessageCommandAnalysisTrait;
use ClayFreeman\IRCParser\Lexer\MessageParametersAnalysisTrait;
use ClayFreeman\IRCParser\Lexer\MessageSourceAnalysisTrait;
use ClayFreeman\IRCParser\Lexer\MessageTagsAnalysisTrait;

use Psr\Http\Message\StreamInterface;

/**
 * Used to perform lexical analysis on an IRCv3 message input.
 */
class Lexer {

  use MessageCommandAnalysisTrait;
  use MessageParametersAnalysisTrait;
  use MessageSourceAnalysisTrait;
  use MessageTagsAnalysisTrait;

  /**
   * Run lexical analysis on the supplied stream and produce a lexeme stream.
   *
   * Messages have this format:
   *   [@tags] [:source] <command> [parameters]
   *
   * The specific parts of an IRC message are:
   *  - tags: Optional metadata on a message, starting with ('@', 0x40)
   *  - source: Optional note of the message origin, starting with (':', 0x3A)
   *  - command: The specific command this message represents
   *  - parameters: If it exists, data relevant to this specific command
   *
   * This method is responsible for delegating the lexical analysis for each
   * message part to a method which produces a generator, and collecting each
   * generator result.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \CachingIterator|\ClayFreeman\IRCParser\Lexeme[]
   *   A sequence of lexemes from the input stream.
   */
  public function analyze(StreamInterface $input): \CachingIterator {
    // Trim any leading 0x20 bytes from the front of the input stream.
    $this->consumeSpace($input);

    // Define the grammatical structure of IRCv3 messages.
    $lexeme_generators = [
      $this->analyzeTags($input),
      $this->analyzeSource($input),
      $this->analyzeCommand($input),
      $this->analyzeParameters($input),
    ];

    // Generate a sequence of lexemes after running the analysis.
    foreach ($lexeme_generators as $lexeme_generator) {
      foreach ($lexeme_generator as $lexeme) {
        $lexemes[] = $lexeme;
      }
    }

    // Use a caching iterator to provide the resulting token stream.
    return new \CachingIterator(new \ArrayIterator($lexemes ?? []), 0);
  }

}
