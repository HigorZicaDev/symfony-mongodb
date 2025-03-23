<?php

namespace App\Controller\Api;

use App\Document\Project;
use App\Document\Diagram;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{
    #[Route('/api/project/create', name: 'create_project', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || empty($data['title'])) {
            return new JsonResponse(['error' => 'Title is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $project = new Project();
        $project->setTitle($data['title']);

        if (isset($data['diagram'])) {
            foreach ($data['diagram'] as $diagramData) {
                $diagram = $this->buildDiagramFromArray($diagramData);
                $project->addDiagram($diagram);
            }
        }

        try {
            $dm->persist($project);
            $dm->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to create project: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Project created', 'id' => $project->getId()]);
    }

    #[Route('/api/project/list', name: 'get_projects', methods: ['GET'])]
    public function getAll(DocumentManager $dm): JsonResponse
    {
        // Busca todos os projetos
        $projects = $dm->getRepository(Project::class)->findAll();

        // Serializa os projetos para JSON
        $data = [];
        foreach ($projects as $project) {
            $data[] = [
                'id' => (string) $project->getId(),
                'title' => $project->getTitle(),
                'diagrams' => $this->getDiagramsData($project->getDiagrams()),
            ];
        }

        // Retorna todos os projetos como resposta JSON
        return $this->json($data);
    }

    #[Route('/api/project/remove/{id}', name: 'remove_project', methods: ['DELETE'])]
    public function remove(string $id, DocumentManager $dm): JsonResponse
    {
        // Busca o projeto pelo ID
        $project = $dm->getRepository(Project::class)->find($id);

        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            // Remove o projeto
            $dm->remove($project);
            $dm->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to remove project: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Project removed successfully'], JsonResponse::HTTP_OK);
    }

    // ROUTES PRIVATES OPERACTIONS INTERN

    private function getDiagramsData($diagrams): array
    {
        $diagramsData = [];
        foreach ($diagrams as $diagram) {
            $diagramsData[] = [
                'title' => $diagram->getTitle(),
                'children' => $this->getDiagramsData($diagram->getChilds()), // Recursão para filhos do diagrama
            ];
        }
        return $diagramsData;
    }

    private function buildDiagramFromArray(array $data): Diagram
    {
        $diagram = new Diagram();
        $diagram->setTitle($data['title'] ?? '');

        if (isset($data['childs'])) {
            foreach ($data['childs'] as $childData) {
                $child = $this->buildDiagramFromArray($childData);
                $diagram->addChild($child);
            }
        }

        return $diagram;
    }
}
