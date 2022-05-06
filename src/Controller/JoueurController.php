<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Form\JoueurType;
use App\Repository\JoueurRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class JoueurController extends AbstractController
{

    const ROUTE_INDEX = "index_joueurs";
    const ROUTE_SHOW = "show_joueur";
    const ROUTE_INSERT = "insert_joueur";
    const ROUTE_UPDATE = "update_joueur";
    const ROUTE_DELETE = "delete_joueur";

    /**
     * @Route("/joueurs/{numPage<\d+>}", name=JoueurController::ROUTE_INDEX)
     * @param JoueurRepository $joueurRepo
     * @param int $numPage
     * @return Response
     */
    public function index(JoueurRepository $joueurRepo, int $numPage = 1): Response
    {
        $limit = 20;
        $offset = $limit * ($numPage - 1);
        try {
            $total = $joueurRepo->getTotaleJoueur();
        } catch (NoResultException $e) {
            return $this->renderError("Il n'existe pas d'entité joueur.", "index");
        } catch (NonUniqueResultException $e) {
            return $this->renderError($e->getMessage(), "index");
        }

        if ($offset < 0 || $offset > $total)
            $offset = 0;

        $joueurs = $joueurRepo->findBy([], ["nom" => "ASC"], $limit, $offset);

        return $this->render('entity/joueur/index.html.twig', [
            'routes' => [
                'index' => JoueurController::ROUTE_INDEX,
                'insert' => JoueurController::ROUTE_INSERT,
                'update' => JoueurController::ROUTE_UPDATE,
                'delete' => JoueurController::ROUTE_DELETE
            ],
            'title' => "Liste des joueurs",
            'joueurs' => $joueurs,
            'currentPage' => $numPage,
            'sum' => $total,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/joueur/{id}",name=JoueurController::ROUTE_SHOW)
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $joueur = $this->getDoctrine()
            ->getRepository(Joueur::class)
            ->find($id);

        if (!$joueur) throw $this->createNotFoundException('Joueur non trouver pour cet id_joueur : ' . $id);

        $titre = "Joueur : " . $joueur->getNom() . " " . $joueur->getPrenom();

        return $this->render('entity/joueur/show.html.twig', [
            'title' => $titre,
            'joueur' => $joueur,
            'routeIndex' => JoueurController::ROUTE_INDEX
        ]);
    }

    /**
     * @Route("/joueur/insert/", name=JoueurController::ROUTE_INSERT, priority="1")
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        $title = "Créer un joueur";
        $labelBtnSave = "Créer";
        $joueur = new Joueur();
        $params = [
            'title' => $title,
            'labelBtnSave' => $labelBtnSave
        ];
        return $this->handleForm($joueur, $request, $params);
    }

    /**
     * @Route ("/joueur/update/{id}" , name=JoueurController::ROUTE_UPDATE)
     * @param int $id
     * @param Request $request
     * @param JoueurRepository $joueurRepo
     * @return Response
     */
    public function update(int $id, Request $request, JoueurRepository $joueurRepo): Response
    {
        $title = "Modifier un joueur";
        $labelBtnSave = "Modifier";
        $joueur = $joueurRepo->find($id);
        $params = [
            'title' => $title,
            'labelBtnSave' => $labelBtnSave
        ];
        return $this->handleForm($joueur, $request, $params);


    }

    /**
     * @Route ("/joueur/delete/{id}" ,name=JoueurController::ROUTE_DELETE)
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $joueurManager = $this->getDoctrine()->getManager();
        $joueur = $joueurManager->getRepository(Joueur::class)->find($id);

        if (!$joueur) {
            throw $this->createNotFoundException(
                'No player found for id ' . $id
            );
        }
        $joueurManager->remove($joueur);
        $joueurManager->flush();
        return $this->redirectToRoute(JoueurController::ROUTE_INDEX);

    }

    /**
     * @param Joueur $joueur
     * @param Request $request
     * @param $params
     * @return Response
     */
    private function handleForm(Joueur $joueur, Request $request, $params) : Response{
        $form = $this->createForm(JoueurType::class, $joueur);
        $form->add("save", SubmitType::class, ['label' => $params['labelBtnSave']]);
        unset($params['labelBtnSave']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($joueur);
            $em->flush();

            return $this->redirectToRoute(JoueurController::ROUTE_SHOW, array('id' => $joueur->getIdJoueur()));
        }

        $params['joueur'] = $joueur;
        $params['form'] = $form->createView();
        $params['pathRetour'] = JoueurController::ROUTE_INDEX;

        return $this->render('entity/joueur/form.html.twig', $params);
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
