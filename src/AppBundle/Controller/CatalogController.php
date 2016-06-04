<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class CatalogController extends Controller
{
    use JsonControllerTrait;

    private $itemsPerPage = 10;

    public function indexAction()
    {
        return $this->render('AppBundle:Catalog:index.html.twig', array(
            // ...
        ));
    }

    private function input($key, $default = null) {
        static $request = null;
        if (null === $request) {
            $request = Request::createFromGlobals();
        }
        return $request->query->get($key, $default);
    }

    protected function calculatePagination($builder) {
        $page = (int)$this->input('page', 1);
        $page = ($page > 1) ? $page : 1; // to prevent error during silly requests with incorrect page number

        $qb = clone $builder;
        $qb->select('count(s.id)');
        $countAll = (int)$qb->getQuery()->getSingleScalarResult();
        unset($qb);

        $firstItem = ($page - 1) ? ($page - 1) * $this->itemsPerPage : 0;
        $lastItem = $page * $this->itemsPerPage;
        if ($firstItem++) {
            $builder->setFirstResult( $firstItem );
        }
        if ($this->itemsPerPage) {
            $builder->setMaxResults( $this->itemsPerPage );
        }

        $lastItem = ($lastItem > $countAll) ? $countAll : $lastItem;
        $countShowed = ($lastItem > $firstItem) ? ($lastItem - $firstItem + 1) : 0;

        if ($this->itemsPerPage) {
            $pageLast = (int)($countAll / $this->itemsPerPage) + (int)(bool)($countAll % $this->itemsPerPage);
        } else {
            $pageLast = $page;
        }

        return [
            'pageCurrent'   => $page,
            'pageLast'      => $pageLast,
            'itemFirst'     => $countShowed ? $firstItem : null,
            'itemLast'      => $countShowed ? $lastItem : null,
            'itemsAll'      => $countAll,
            'itemsShowed'   => $countShowed,
        ];
    }

    public function getAction()
    {
        $em = $this->get('doctrine')->getManager('default');
        $repository = $em->getRepository('AppBundle:Music\Songs');

        $aName = $this->input('artist');
        $gName = $this->input('genre');

        $builder = $repository
            ->createQueryBuilder('s')
            ->join('s.artist', 'a')
            ->join('a.genre', 'g');

        if (isset($aName[0])) {
            $builder->where('a.name = :aName')->setParameter(':aName', $aName);
        }

        if (isset($gName[0])) {
            $builder->andWhere('g.name = :gName')->setParameter(':gName', $gName);
        }

        $pagination = $this->calculatePagination($builder);

        try {
            $items = $builder->getQuery()->getResult();
            //$items = $repository->findBy(array()); // tmp // debug
    
            $items = array_map(function ($entity) {
                return [
                    'id'        => $entity->getID(),
                    'artist'    => $entity->getArtist()->getName(),
                    'song'      => $entity->getName(),
                    'genre'     => $entity->getArtist()->getGenre()->getName(),
                    'year'      => $entity->getYear(),
                ];
            }, $items);

            $data = compact('items', 'pagination');
        
            return $this->jsonSuccessResponse($data);
        } catch (Exception $e) {
            return $this->jsonFailureResponse([], $e->getMessage());
        }
    }

}
