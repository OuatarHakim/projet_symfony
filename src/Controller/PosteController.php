<?php

namespace App\Controller;

use App\Repository\PosteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Poste;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PosteType;


class PosteController extends AbstractController
{
    const ROUTE_INDEX = "index_postes";
    const ROUTE_SHOW = "show_poste";
    const ROUTE_INSERT = "insert_poste";
    const ROUTE_UPDATE = "update_poste";
    const ROUTE_DELETE = "delete_poste";

    /**
     * @Route("/postes/{numPage<\d+>}", name=PosteController::ROUTE_INDEX)
     * @param PosteRepository $posteRepository
     * @param int $numPage
     * @return Response
     */
    public function index(PosteRepository $posteRepository, int $numPage = 1): Response
    {
        $limit = 10;
        $offset = $limit * ($numPage - 1);

        try {
            $total = $posteRepository->getNbPostes();
        } catch (NoResultException $e) {
            return $this->renderError("Il n'existe pas d'entité poste.", "index");
        } catch (NonUniqueResultException $e) {
            return $this->renderError($e->getMessage(), "index");
        }

        if ($offset < 0 || $offset > $total)
            $offset = 0;

        $postes = $posteRepository->findBy([], null, $limit, $offset);

        return $this->render('entity/poste/index.html.twig', [
            'routes' => [
                'index' => PosteController::ROUTE_INDEX,
                'insert' => PosteController::ROUTE_INSERT,
                'update' => PosteController::ROUTE_UPDATE,
                'delete' => PosteController::ROUTE_DELETE
            ],
            'title' => "Liste des postes",
            'postes' => $postes,
            'currentPage' => $numPage,
            'sum' => $total,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/poste/{id}",name=PosteController::ROUTE_SHOW)
     * @param int $id
     * @return Response
     */

    public function show(int $id): Response
    {
        $poste = $this->getDoctrine()
            ->getRepository(Poste::class)
            ->find($id);

        if (!$poste) {
            throw $this->createNotFoundException('poste non trouver pour cet id_poste : ' . $id);
        }
        return $this->render('entity/poste/show.html.twig', [
            'title' => "Poste " . $poste->getNom(),
            'poste' => $poste,
            'routeIndex' => PosteController::ROUTE_INDEX
        ]);
    }

    /**
     * @Route("/poste/insert/", name=PosteController::ROUTE_INSERT, priority="1")
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        $title = "Créer un poste";
        $labelBtnSave = "Créer";
        $poste = new Poste();
        return $this->handleForm($poste, $request, ['title'=>$title, 'labelBtnSave'=>$labelBtnSave]);

    }

    /**
     * @Route ("/poste/update/{id}" , name=PosteController::ROUTE_UPDATE)
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function update(int $id, Request $request, PosteRepository $repo): Response
    {
        $title = "Modifier un poste";
        $labelBtnSave = "Modifier";
        $poste = $repo->find($id);
        return $this->handleForm($poste, $request, ['title'=>$title, 'labelBtnSave'=>$labelBtnSave]);
    }

    /**
     * @Route ("/poste/delete/{id}", name=PosteController::ROUTE_DELETE)
     * @param string $id
     * @return RedirectResponse
     */
    public function delete(string $id): RedirectResponse
    {
        $posteManager = $this->getDoctrine()->getManager();
        $poste = $posteManager->getRepository(Poste::class)->find($id);

        if (!$poste) {
            throw $this->createNotFoundException(
                'No poste found for id ' . $id
            );
        }
        $posteManager->remove($poste);
        $posteManager->flush();
        return $this->redirectToRoute(PosteController::ROUTE_INDEX);
    }

    /**
     * @param Poste $poste
     * @param Request $request
     * @param $params
     * @return Response
     */
    private function handleForm(Poste $poste, Request $request, $params) : Response
    {
        $form = $this->createForm(PosteType::class, $poste);
        $form->add("save", SubmitType::class, ['label' => $params['labelBtnSave']]);
        unset($params['labelBtnSave']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($poste);
            $em->flush();

            return $this->redirectToRoute(PosteController::ROUTE_SHOW, array('id' => $poste->getIdPoste()));
        }

        $params['poste'] = $poste;
        $params['form'] = $form->createView();
        $params['pathRetour'] = PosteController::ROUTE_INDEX;

        return $this->render('entity/poste/form.html.twig', $params);
    }

    /**
     * @param string $errorMsg
     * @param string $routePrevious
     * @param array $params
     * @return Response
     */
    private function renderError(string $errorMsg, string $routePrevious, array $params = []) : Response
    {
        return $this->render('entity/error.html.twig', [
            'error' => $errorMsg,
            'routePrevious' => $routePrevious,
            'paramsRoute' => $params
        ]);
    }
}
