<?php

namespace App\Controller;

use App\Service\InlineCalculatorService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InlineCalculatorController extends AbstractFOSRestController
{
    public function __construct(private InlineCalculatorService $service) {
    }

    /**
     * Cet appel api permet de calculer des opérations mathématiques,
     * en se basant sur les mots des opérateurs et nombre entier de 0 à 9 en anglais.
     */
    #[Route('/api/process', methods: ['POST'])]
    #[OA\Tag(name: 'Calculator')]
    #[OA\Post(
        path: '/api/process',
        responses: [
            new OA\Response(response: 200, description: 'Resultat de l\'opération'),
            new OA\Response(response: 400, description: 'Bad request'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Expression json représentant les mots d\'une opération en ANGLAIS',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'expression', type:'string', example: 'nine plus nine times two')
                ]
            ),
        )
    )]
    public function process(Request $request): JsonResponse
    {
        try {

            $dataToProcess = json_decode($request->getContent(), true);
            $process = $this->service->process($dataToProcess);

            return new JsonResponse($process, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}