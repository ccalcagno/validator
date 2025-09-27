<?php

declare(strict_types=1);

namespace Calcagno\Validator;

/**
 * Trait ValidateAttributes
 *
 * Fornece métodos utilitários para validação de atributos comuns
 * em entidades e DTOs, incluindo CPF, CNPJ, telefone e valores obrigatórios.
 * Também contém um método de cálculo genérico de dígito verificador (modulo 10/11).
 *
 * @package Calcagno\Validator
 */
trait ValidateAttributes
{
  /**
   * Valida se um CPF é válido.
   *
   * @param mixed $value Valor a ser validado (string esperada, pode conter pontuação).
   * @return bool Retorna true se o CPF for válido ou se for nulo/vazio, caso contrário false.
   */
  protected function isValidCpf(mixed $value): bool
  {
    if ($value === null || $value === '') {
      return true;
    }

    if (!is_string($value) || !preg_match('/^\d{11}$|^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $value)) {
      return false;
    }

    $value = preg_replace('/\D/', '', $value);

    if (mb_strlen($value) != 11) {
      return false;
    }

    if (preg_match('/^(.)\1*$/', $value)) {
      return false;
    }

    $firstDigit = $this->modulo10(substr($value, 0, 9));
    if ((int)$value[9] !== $firstDigit) {
      return false;
    }

    $secondDigit = $this->modulo10(substr($value, 0, 10));
    if ((int)$value[10] !== $secondDigit) {
      return false;
    }

    return true;
  }

  /**
   * Valida se um CNPJ é válido.
   *
   * @param mixed $value Valor a ser validado (string esperada, pode conter pontuação).
   * @return bool Retorna true se o CNPJ for válido ou se for nulo/vazio, caso contrário false.
   */
  private function validateCnpj(mixed $value): bool
  {
    if (!is_string($value)) {
      return false;
    }

    if (is_null($value) || empty($value)) {
      return true;
    }

    $c = preg_replace('/\D/', '', $value);
    $b = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);

    if (mb_strlen($c) != 14) {
      return false;
    }

    if (preg_match('/^(.)\1*$/', $value)) {
      return false;
    }

    for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);

    if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
      return false;
    }

    for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);

    if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
      return false;
    }

    return true;
  }

  /**
   * Valida se um telefone é válido.
   *
   * @param mixed $value Número de telefone (string esperada).
   * @return bool Retorna true se o telefone for válido, ou se for nulo/vazio; 
   */
  public function validatePhone(mixed $value): bool
  {
    if (!is_string($value)) {
      return false;
    }

    if (is_null($value) || empty($value)) {
      return true;
    }

    return preg_match('/^(\(0?\d{2}\)\s?|0?\d{2}[\s.-]?)\d{4,5}[\s.-]?\d{4}$/', $value);
  }

  /**
   * Valida se um valor obrigatório está presente.
   *
   * @param mixed $value Valor a ser verificado.
   * @return bool Retorna false se o valor for nulo, vazio ou coleções vazias, true caso contrário.
   */
  public function validateRequired(mixed $value): bool
  {
    if (is_null($value)) {
      return false;
    }

    if ((is_string($value) || is_countable($value)) && empty($value)) {
      return false;
    }

    return (is_numeric($value)) ? (bool)$value : true;
  }

  /**
   * Calcula o dígito verificador utilizando o algoritmo de módulo 11
   * com pesos progressivos a partir de 2. Esse método é genérico e pode
   * ser aplicado em diferentes validações de documentos (ex.: CPF, CNPJ, etc).
   *
   * @param string $input Sequência numérica usada para o cálculo.
   * @return int Dígito verificador resultante do cálculo.
   */
  private function modulo10(string $input): int
  {
    $input = preg_replace('/\D/', '', $input);
    $input = strrev($input);

    $sum = 0;
    $fator = 2;

    foreach (str_split($input) as  $value) {
      $sum += (int)$value * $fator;
      $fator++;
    }

    $mod = $sum % 11;
    return ($mod < 2) ? 0 : 11 - $mod;
  }
}
