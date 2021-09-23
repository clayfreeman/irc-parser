<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

use ClayFreeman\IRCParser\Parser\MessageTagsParserTrait;

use Psr\Http\Message\StreamInterface;

/**
 * Used to parse an IRCv3 message from a lexeme stream.
 */
class Parser {

  use MessageTagsParserTrait;

  /**
   * An IRCv3 message lexer.
   *
   * @var \ClayFreeman\IRCParser\Lexer
   */
  protected Lexer $lexer;

  /**
   * Constructs a Parser object.
   *
   * @param \ClayFreeman\IRCParser\Lexer $lexer
   *   An IRCv3 message lexer.
   */
  public function __construct(Lexer $lexer) {
    $this->lexer = $lexer;
  }

  /**
   * Parse the supplied input stream as an IRCv3 message.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream to parse.
   *
   * @return mixed
   *   TODO.
   */
  public function parse(StreamInterface $input) {
    $stream = $this->lexer->analyze($input);

    $tags = $this->parseTags($stream);

    eval(\Psy\sh());
  }

}
