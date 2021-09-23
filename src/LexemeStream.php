<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Provides a wrapper used to treat a lexeme sequence as a stream.
 */
class LexemeStream {

  /**
   * The lexeme sequence to represent as a stream.
   *
   * @var \ClayFreeman\IRCParser\Lexeme[]
   */
  protected $lexemes = [];

  /**
   * Constructs a LexemeStream object.
   *
   * @param \ClayFreeman\IRCParser\Lexeme[] $lexemes
   *   The lexeme sequence to represent as a stream.
   */
  public function __construct(array $lexemes) {
    $this->lexemes = $lexemes;
  }

  /**
   * Consumes a lexeme from the front of the stream.
   *
   * @return \ClayFreeman\IRCParser\Lexeme|null
   *   A lexeme from the front of the stream, or NULL if the stream is empty.
   */
  public function consume(): ?Lexeme {
    if ($lexeme = array_shift($this->lexemes)) {
      return $lexeme;
    }

    return NULL;
  }

  /**
   * Check if the lexeme stream is empty.
   *
   * @return bool
   *   TRUE if the stream is empty, false otherwise.
   */
  public function empty(): bool {
    return count($this->lexemes) === 0;
  }

  /**
   * Peek at the first lexeme in the stream.
   *
   * @return \ClayFreeman\IRCParser\Lexeme|null
   *   The first lexeme in the stream, or NULL if the stream is empty.
   */
  public function peek(): ?Lexeme {
    if ($lexeme = reset($this->lexemes)) {
      return $lexeme;
    }

    return NULL;
  }

}
