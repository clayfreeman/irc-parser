<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Enumerates the byte classes used to match data in the lexer input stream.
 */
enum ByteClass: string implements ByteClassInterface {

  use ByteClassTrait;

  case AlphaLower = 'abcdefghijklmnopqrstuvwxyz';
  case AlphaUpper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  case Digit = '0123456789';
  case Hyphen = '-';
  case Space = ' ';
  case Special = '[\\]^_`{|}';

  /**
   * Creates a composite byte class from constituent byte class components.
   *
   * @param \ClayFreeman\IRCParser\ByteClassInterface ...$classes
   *   A list of byte classes used to create the composite byte class.
   *
   * @return \ClayFreeman\IRCParser\CompositeByteClass
   *   The composite byte class.
   */
  public static function create(ByteClassInterface ...$classes) {
    return new CompositeByteClass($classes);
  }

}
