<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser\Value;

/**
 * Represents an IRCv3 message source.
 */
class Source {

  /**
   * The host associated with the source client.
   *
   * @var string|null
   */
  public ?string $host = NULL;

  /**
   * The principal element of the message source.
   *
   * If accompanied by a user or host, then it's safe to assume that the
   * principal represents a nickname.
   *
   * @var string
   */
  public string $principal = '';

  /**
   * The user associated with the source client (also known as "ident").
   *
   * @var string|null
   */
  public ?string $user = NULL;

  /**
   * Constructs a Source object.
   *
   * @param string $principal
   *   The principal element of the message source.
   */
  public function __construct(string $principal) {
    $this->principal = $principal;
  }

  /**
   * Determines whether the message source represents a regular client.
   *
   * In some cases, this method may yield a false-negative result. It is
   * possible that the message source is ambiguous if not enough information is
   * available to make an assessment.
   *
   * This method will return TRUE if a host was supplied in addition to the
   * principal element.
   *
   * @return bool
   *   TRUE if the source represents a client, FALSE otherwise.
   */
  public function isClient(): bool {
    return isset($this->host);
  }

  /**
   * Get the nickname from this source.
   *
   * If the source has not been established as a client, this method will return
   * NULL since the source may represent a server.
   *
   * @return string|null
   *   The nickname, or NULL if one is not available.
   */
  public function nick(): ?string {
    return $this->isClient() ? $this->principal : NULL;
  }

  /**
   * Renders the message source in the format specified by IRCv3.
   *
   * If a user is set without a host, only the principal will be rendered.
   *
   * @return string
   *   The message source rendered in the format specified by IRCv3.
   */
  public function render(): string {
    $user = isset($this->user, $this->host) ? "!{$this->user}" : '';
    $host = isset($this->host) ? "@{$this->host}" : '';

    return $this->principal . $user . $host;
  }

}
