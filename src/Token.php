<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Enumerates the types of IRCv3 tokens.
 */
enum Token: string {

  /**
   * Used to identify the message command.
   */
  case Command = 'command';

  /**
   * Used to identify command parameters.
   */
  case Parameter = 'parameter';

  /**
   * Used to identify the message source principal.
   *
   * Lexemes of this token type may be ambiguous if they exist alone, as the
   * lexeme could represent either a nickname or server.
   *
   * If accompanied by a succesive source host or source user token, then the
   * lexeme with this token type can safely be assumed to be a nickname.
   *
   * Lexemes can also be resolved if they contain characters that do not
   * constitute a valid hostname, but do constitute a valid nickname (and vice
   * versa); however, this assumption is slightly less safe since it cannot be
   * afforded confirmation by grammatical structure.
   */
  case Source = 'source';

  /**
   * Used to identify the message source host.
   */
  case SourceHost = 'source_host';

  /**
   * Used to identify the message source user.
   */
  case SourceUser = 'source_user';

  /**
   * Used to identify a message tag vendor or name.
   *
   * Lexemes of this token type may be followed by a more specific tag name
   * token, in which case this lexeme represents the tag vendor. Otherwise, this
   * lexeme represents the tag name.
   */
  case Tag = 'tag';

  /**
   * Used to identify a message tag name.
   */
  case TagName = 'tag_name';

  /**
   * Used to signify that the following tag lexeme sequence is client-only.
   */
  case TagPrefixClient = 'tag_prefix_client';

  /**
   * Used to identify a message tag value.
   */
  case TagValue = 'tag_value';

  /**
   * Convert the supplied value to the appropriate data type for the token.
   *
   * @param string|null $value
   *   The value to convert to the appropriate data type.
   *
   * @return mixed
   *   The supplied value, casted to the appropriate data type for the token.
   */
  public function convert(?string $value): mixed {
    return match($this) {
      Token::Command => is_numeric($value) ? intval($value) : strtoupper($value ?? ''),
      Token::TagValue => isset($value) ? $this->decodeString($value) : '',
      default => $value,
    };
  }

  /**
   * Convert the supplied string into its decoded form.
   *
   * @param string $value
   *   The encoded string value to decode.
   *
   * @return string
   *   The decoded string value.
   */
  protected function decodeString(string $value): string {
    // To preserve the double-backslash escape sequence, we must split the
    // string when it occurs to facilitate the other transformations.
    $value = explode('\\\\', $value);

    // Apply the other transformations to each string segment.
    $value = array_map(function ($value) {
      $mapping = [
        '\\:' => ';',
        '\\s' => ' ',
        '\\r' => "\r",
        '\\n' => "\n",
      ];

      // Apply the escape sequence mapping to the input value.
      foreach ($mapping as $encoded => $decoded) {
        $value = str_replace($encoded, $decoded, $value);
      }

      // Process invalid escape sequences by removing the leading backslash.
      return str_replace('\\', '', $value);
    }, $value);

    // Join each resulting segment with a backslash byte.
    return implode('\\', $value);
  }

}
