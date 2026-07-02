<?php

declare(strict_types=1);

namespace Calcagno\Validator;

/**
 * Trait com os métodos de validação de datas do Validator.
 *
 * Depende do estado interno da classe hospedeira — não deve ser usada
 * isoladamente, apenas como parte de Calcagno\Validator\Validator.
 *
 * @property array $_data     Dado atual sendo validado ('name' e 'value')
 * @property array $_messages Mensagens de erro configuráveis por método
 */
trait ValidatesDates
{
  abstract protected function set_error($error);

  /**
   * Verify if the current data is a valid Date
   * @access public
   * @param string|null $format [optional] The Date format
   * @return static The self instance
   */
  public function is_date($format = null)
  {
    $verify = true;

    if ($this->_data['value'] instanceof \DateTime) {
      return $this;
    } elseif (!is_string($this->_data['value'])) {
      $verify = false;
    } elseif (is_null($format)) {
      $verify = (strtotime($this->_data['value']) !== false);
      if ($verify) {
        return $this;
      }
    }

    if ($verify) {
      $date_from_format = \DateTime::createFromFormat($format, $this->_data['value']);
      $verify = $date_from_format && $this->_data['value'] === date($format, $date_from_format->getTimestamp());
    }

    if (!$verify) {
      $this->set_error(sprintf($this->_messages['is_date'], $this->_data['value']));
    }

    return $this;
  }

  /**
   * Verify if the current data is after the given date
   * @access public
   * @return static The self instance
   */
  public function is_after(mixed $date, ?string $format = null)
  {
    return $this->compareDate(
      $date,
      $format,
      static fn(int $valueTs, int $compareTs): bool => $valueTs > $compareTs,
      'is_after'
    );
  }

  /**
   * Verify if the current data is after or equal to the given date
   * @access public
   * @return static The self instance
   */
  public function is_after_or_equals(mixed $date, ?string $format = null): static
  {
    return $this->compareDate(
      $date,
      $format,
      static fn(int $valueTs, int $compareTs): bool => $valueTs >= $compareTs,
      'is_after_or_equals'
    );
  }

  /**
   * Verify if the current data is before the given date
   * @access public
   * @return static The self instance
   */
  public function is_before(mixed $date, ?string $format = null): static
  {
    return $this->compareDate(
      $date,
      $format,
      static fn(int $valueTs, int $compareTs): bool => $valueTs < $compareTs,
      'is_before'
    );
  }

  /**
   * Verify if the current data is before or equal to the given date
   * @access public
   * @return static The self instance
   */
  public function is_before_or_equals(mixed $date, ?string $format = null): static
  {
    return $this->compareDate(
      $date,
      $format,
      static fn(int $valueTs, int $compareTs): bool => $valueTs <= $compareTs,
      'is_before_or_equals'
    );
  }

  /**
   * Lógica compartilhada entre is_after / is_after_or_equals / is_before /
   * is_before_or_equals. As quatro só diferem no operador de comparação
   * (passado via $comparator) e na chave da mensagem de erro.
   *
   * @access private
   * @param mixed $date A data de comparação (string ou DateTime)
   * @param string|null $format Formato esperado, se houver
   * @param callable(int, int): bool $comparator Recebe (valueTs, compareTs)
   * @param string $messageKey Chave em $this->_messages para o erro
   * @return static The self instance
   */
  private function compareDate(mixed $date, ?string $format, callable $comparator, string $messageKey): static
  {
    $verify = false;

    if (is_null($format)) {
      $value_ts = $this->_data['value'] instanceof \DateTime
        ? $this->_data['value']->getTimestamp()
        : strtotime((string) $this->_data['value']);

      $compare_ts = $date instanceof \DateTime
        ? $date->getTimestamp()
        : strtotime((string) $date);
    } else {
      $value_date = $this->_data['value'] instanceof \DateTime
        ? $this->_data['value']
        : \DateTime::createFromFormat($format, (string) $this->_data['value']);

      $compare_date = $date instanceof \DateTime
        ? $date
        : \DateTime::createFromFormat($format, (string) $date);

      $value_ts = $value_date ? $value_date->getTimestamp() : false;
      $compare_ts = $compare_date ? $compare_date->getTimestamp() : false;
    }

    if ($value_ts !== false && $compare_ts !== false) {
      $verify = $comparator($value_ts, $compare_ts);
    }

    if (!$verify) {
      $this->set_error(sprintf($this->_messages[$messageKey], $this->_data['value'], $date));
    }

    return $this;
  }
}
