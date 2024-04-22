<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InlineCalculatorServiceTest extends KernelTestCase
{

    /**
     * @var InlineCalculatorService
     */
    private $service;

    public function setUp(): void 
    {
        $this->service = new InlineCalculatorService();
    }

    public function testProcessOK() {
        $dataToProcess = [
            'expression' => 'nine plus eight plus two times three'
        ];
        
        $actualResultProcess = $this->service->process($dataToProcess);
        $this->assertIsArray($actualResultProcess);
        $this->assertEquals(23, $actualResultProcess['result']);
        $this->assertEquals('9 + 8 + 2 * 3', $actualResultProcess['operation']);
        $this->assertEquals('nine plus eight plus two times three', $actualResultProcess['expression']);
    }
    
    public function testProcessEmptyExpression() {
        try {
            $dataToProcess = ['nine plus eight plus two times three'];
            $this->service->process($dataToProcess);
        } catch (\Exception $e ) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('Erreur de traitement : le champ expression doit être rempli', $e->getMessage());
        }
    }

    public function testProcessIncorrecteExpression() {
        try {
            $dataToProcess = ['expression' => 'niane plus eight plus two times three'];
            $this->service->process($dataToProcess);
        } catch (\Exception $e ) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('Erreur de traitement : L\'expression "niane plus eight plus two times three" est incorrecte.', $e->getMessage());
        }
    }

    public function testProcesDivisionZeroExpression() {
        try {
            $dataToProcess = ['expression' => 'nine plus eight plus two times three divided zero'];
            $this->service->process($dataToProcess);
        } catch (\Exception $e ) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('Erreur de traitement du calcul 9 + 8 + 2 * 3 / 0 : Division par 0 détectée.', $e->getMessage());
        }
    }
}
