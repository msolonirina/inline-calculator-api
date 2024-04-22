<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InlineCalculatorService
{
    private const ACCEPTS = [
        'plus'    => '+',
        'minus'   => '-',
        'divided' => '/',
        'times'   => '*',
        'zero'  => 0,
        'one'   => 1,
        'two'   => 2,
        'three' => 3,
        'four'  => 4,
        'five'  => 5,
        'six'   => 6,
        'seven' => 7,
        'eight' => 8,
        'nine'  => 9
    ];


    public function process(array $input): array {

        if (!array_key_exists('expression', $input) || empty($expression = $input['expression'])) {
            throw new BadRequestHttpException('Erreur de traitement : le champ expression doit être rempli');
        }

        $explodeProcess = explode(' ', trim($expression));
        $keyErrors = [];
        $dataToProcess = array_map(
            function ($key)  use (&$keyErrors) {
                if (in_array($key, array_keys(self::ACCEPTS))) {
                    return ($key == 'zero' ? '0' : self::ACCEPTS[$key]) ?? null;
                }

                $keyErrors[] = $key;
            },
            $explodeProcess
        );

        // Vérifie que l'expression est correcte 
        if (!empty($keyErrors)) {
            throw new BadRequestHttpException(sprintf("Erreur de traitement : L'expression \"%s\" est incorrecte.", $expression));
        }

        $dataToEval = implode(' ', $dataToProcess);

        // Vérification si l'expression commence ou finit par un opérateur
        if (preg_match('/^[\/*+-]|[\/*+-]$/', $dataToEval)) {
            throw new BadRequestHttpException(sprintf("Erreur de traitement du calcul %s : L'expression ne peut pas commencer ou finir par un opérateur.", $dataToEval));
        }

        // Vérification si deux nombres ou deux opérateurs se suivent
        if (preg_match('/\d{2,}|[\/*+-]{2,}/', str_replace(' ', '', $dataToEval))) {
            throw new BadRequestHttpException(sprintf("Erreur de traitement du calcul %s : Deux nombres ou deux opérateurs se suivent.", $dataToEval));
        }

        // Vérification division par zéro
        if (strpos(str_replace(' ', '', $dataToEval), '/0') !== false) {
            throw new BadRequestHttpException(sprintf("Erreur de traitement du calcul %s : Division par 0 détectée.", $dataToEval));
        }

        return [
            'expression' => trim($expression),
            'operation'  => $dataToEval,
            'result'     => eval(sprintf('return %s ;', $dataToEval))
        ];
    }
}
