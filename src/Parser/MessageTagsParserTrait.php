<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser;

use ClayFreeman\IRCParser\LexemeStream;
use ClayFreeman\IRCParser\Token;
use ClayFreeman\IRCParser\Parser\Value\Tag;

/**
 * A trait used to parse a sequence of message tag lexemes.
 */
trait MessageTagsParserTrait {

  use BaseParserTrait;

  /**
   * Parse a tag from the supplied lexeme stream.
   *
   * This method expects that the presence of a tag has been established prior
   * to being invoked. This can be done by ensuring that the next lexeme token
   * represents a tag or a client-only tag prefix.
   *
   * @param \ClayFreeman\IRCParser\LexemeStream $stream
   *   The lexeme stream from which to parse a tag.
   *
   * @return \ClayFreeman\IRCParser\Parser\Value\Tag
   *   A tag parsed from the lexeme stream.
   */
  private function parseTag(LexemeStream $stream): Tag {
    $current = $stream->consume();
    $tag = new Tag();

    // Determine whether this is a client-only tag.
    if ($current->token === Token::TagPrefixClient) {
      $current = $this->consume($stream, Token::Tag);
      $tag->clientOnly = TRUE;
    }

    // By default, assume that a vendor namespace was not specified.
    $tag->name = $current->value;

    // Determine whether a vendor namespace was specified for this tag.
    if ($stream->peek()?->token === Token::TagName) {
      $tag->vendor = $current->value;
      $tag->name = $stream->consume()->value;
    }

    // Determine whether a value was specified for this token.
    if ($stream->peek()?->token === Token::TagValue) {
      $tag->value = $stream->consume()->value;
    }

    return $tag;
  }

  /**
   * Attempt to parse a tag list from the supplied lexeme stream.
   *
   * If no tag list is present, this method will produce an empty tag list.
   *
   * @param \ClayFreeman\IRCParser\LexemeStream $stream
   *   The lexeme stream from which to parse an optional tag list.
   *
   * @return \ClayFreeman\IRCParser\Parser\Value\Tag[]
   *   A list of tags parsed from the lexeme stream.
   */
  protected function parseTags(LexemeStream $stream): array {
    $tags = [];

    $tokens = [Token::Tag, Token::TagPrefixClient];
    while (in_array($stream->peek()?->token, $tokens, TRUE)) {
      $tags[] = $this->parseTag($stream);
    }

    return $tags;
  }

}
