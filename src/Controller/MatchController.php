<?php


namespace App\Controller;


use App\Entity\Match;
use App\Entity\Equipe;
use App\Form\MatchType;
use App\Repository\MatchRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{
    const ROUTE_INDEX = "index_matchs";
    const ROUTE_SHOW = "show_match";
    const ROUTE_INSERT = "insert_match";
    const ROUTE_UPDATE = "update_match";
    const ROUTE_DELETE = "delete_match";

    /**
     * @Route("/matchs/{numPage<\d+>}", name=MatchController::ROUTE_INDEX);
     * @param MatchRepository $matchRepo
     * @param int $numPage
     * @return Response
     */
    public function index(MatchRepository $matchRepo, int $numPage = 1): Response
    {
        $limit = 10;
        $offset = $limit * ($numPage - 1);

        try {
            $total = $matchRepo->getNbMatchs();
        } catch (NoResultException $e) {
            return $this->renderError("Il n'existe pas d'entité match.", "index");
        } catch (NonUniqueResultException $e) {
            return $this->renderError($e->getMessage(), "index");
        }

        if ($offset < 0 || $offset > $total)
            $offset = 0;

        $matchs = $matchRepo->findBy([], ["date" => "ASC"], $limit, $offset);

        return $this->render('entity/match/index.html.twig', [
            'routes' => [
                'index' => MatchController::ROUTE_INDEX,
                'insert' => MatchController::ROUTE_INSERT,
                'update' => MatchController::ROUTE_UPDATE,
                'delete' => MatchController::ROUTE_DELETE
            ],
            'title' => "Liste des matchs",
            'matchs' => $matchs,
            'currentPage' => $numPage,
            'sum' => $total,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/match/{idEquipe1}/[idEquipe2}/{date}", name=MatchController::ROUTE_SHOW);
     * @param string $idEquipe1
     * @param string $idEquipe2
     * @param string $date
     * @return Response
     */
    public function show(string $idEquipe1, string $idEquipe2, string $date): Response
    {

        $match = $this->getMatch($idEquipe1, $idEquipe2, $date);

        $titre = "Match du " . $match->getDate() . " entre les équipes " . $match->getEquipe1()->getIdEquipe() . " et " .
            $match->getEquipe2()->getIdEquipe();

        return $this->render('entity/match/show.html.twig', [
            'title' => $titre,
            'match' => $match,
            'routeIndex' => MatchController::ROUTE_INDEX
        ]);
    }

    /**
     * @Route("/match/insert", name=MatchController::ROUTE_INSERT)
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        $match = new Match();

        $form = $this->createForm(MatchType::class, $match);
        $form->add("save", SubmitType::class, ['label' => "Créer"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($match);
                $em->flush();
            }catch (UniqueConstraintViolationException $e) {
                return $this->renderError("Un match contenant les mêmes clés existent déjà !", MatchController::ROUTE_INSERT);
            } catch (Exception $e) {
                return $this->renderError($e->getMessage(), MatchController::ROUTE_INSERT);
            }

            return $this->show($match->getEquipe1()->getIdEquipe(), $match->getEquipe2()->getIdEquipe(), $match->getDate());
        }
        return $this->render('entity/match/form.html.twig', array(
            'title' => "Créer un nouveau match",
            'match' => $match,
            'form' => $form->createView(),
            'pathRetour' => MatchController::ROUTE_INDEX
        ));
    }

    /**
     * @Route("/match/update/{idEquipe1}/{idEquipe2}/{date}", name=MatchController::ROUTE_UPDATE)
     * @param string $idEquipe1
     * @param string $idEquipe2
     * @param string $date
     * @param Request $request
     * @return Response
     */
    public function update(string $idEquipe1, string $idEquipe2, string $date, Request $request): Response
    {
        try {
            $match = $this->getMatch($idEquipe1, $idEquipe2, $date);
        } catch (InvalidArgumentException|NotFoundHttpException $e) {
            return $this->renderError($e->getMessage(), MatchController::ROUTE_INDEX);
        }

        $form = $this->createForm(MatchType::class, $match);
        $form->add("save", SubmitType::class, ['label' => "Modifier"]);

        $paramDate = $form->get("date")->getConfig()->getOptions();
        $paramDate['attr']['readonly'] = 'readonly';
        $paramDate['disabled']='disabled';

        $paramEquipe = $form->get("equipe1")->getConfig()->getOptions();
        $paramEquipe['attr']['readonly'] = "readonly";
        $paramEquipe['disabled'] = 'disabled';

        $form->add("date", DateType::class, $paramDate)
            ->add("equipe1", EntityType::class, $paramEquipe)
            ->add("equipe2", EntityType::class, $paramEquipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($match);
            $em->flush();

            return $this->show($match->getEquipe1()->getIdEquipe(), $match->getEquipe2()->getIdEquipe(), $match->getDate());
        }
        return $this->render('entity/match/form.html.twig', array(
            'title' => "Modifier un match",
            'match' => $match,
            'form' => $form->createView(),
            'pathRetour' => MatchController::ROUTE_INDEX
        ));
    }

    /**
     * @Route("/match/delete/{idEquipe1}/{idEquipe2}/{date}", name=MatchController::ROUTE_DELETE)
     * @param string $idEquipe1
     * @param string $idEquipe2
     * @param string $date
     * @return Response
     */
    public function delete(string $idEquipe1, string $idEquipe2, string $date): Response
    {
        try {
            $match = $this->getMatch($idEquipe1, $idEquipe2, $date);
        } catch (InvalidArgumentException|NotFoundHttpException $e) {
            return $this->renderError($e->getMessage(), MatchController::ROUTE_INDEX);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($match);
            $em->flush();
        } catch (Exception $e) {
            return $this->renderError($e->getMessage(), MatchController::ROUTE_INDEX);
        }

        return $this->redirectToRoute(MatchController::ROUTE_INDEX);
    }

    /**
     * @param string $idEquipe1
     * @param string $idEquipe2
     * @param string $date
     * @return Match, objet de type Match.
     */
    private function getMatch(string $idEquipe1, string $idEquipe2, string $date)
    {
        if ($idEquipe1 === $idEquipe2) throw new InvalidArgumentException("Une équipe ne peut pas jouer contre elle-même !");

        $equipeRepo = $this->getDoctrine()->getRepository(Equipe::class);

        $equipe1 = $equipeRepo->find($idEquipe1);
        if (!$equipe1) throw $this->createNotFoundException("Aucune équipe ne correspond à $idEquipe1 !");
        $equipe2 = $equipeRepo->find($idEquipe2);
        if (!$equipe2) throw $this->createNotFoundException("Aucune équipe ne correspond à $idEquipe2 !");

        $match = $this->getDoctrine()->getRepository(Match::class)->findOneBy([
            "equipe1" => $equipe1,
            "equipe2" => $equipe2,
            "date" => $date
        ]);

        if (!$match) {
            throw $this->createNotFoundException("Match non trouvé avec ces paramètres : $idEquipe1, $idEquipe2 et $date");
        }

        return $match;
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