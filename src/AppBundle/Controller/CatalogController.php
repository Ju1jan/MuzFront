<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class CatalogController extends Controller
{
    use JsonControllerTrait;

    protected $itemsPerPage = 10;

    function __construct()
    {
        // `showed` - items per page
        $this->itemsPerPage = (int)$this->input('showed', $this->itemsPerPage);
    }

    public function indexAction()
    {
        $maxItems = 80; // just inc case // <- TODO: move in config
        $em = $this->get('doctrine')->getManager('default');

        $repository = $em->getRepository('AppBundle:Music\Artists');
        $builder = $repository->createQueryBuilder('a')
            ->select('a.id, a.name')
            ->groupBy('a.name')
            ->setMaxResults($maxItems);
        $artists = $builder->getQuery()->getResult();

        $repository = $em->getRepository('AppBundle:Music\Songs');
        $builder = $repository->createQueryBuilder('s')
            ->select('s.id, s.year')
            ->groupBy('s.year')
            ->setMaxResults($maxItems);
        $years = $builder->getQuery()->getResult();

        $repository = $em->getRepository('AppBundle:Music\Genres');
        $builder = $repository->createQueryBuilder('g')
            ->select('g.id, g.name')
            ->groupBy('g.name')
            ->setMaxResults($maxItems);
        $genres = $builder->getQuery()->getResult();

        $repository = $em->getRepository('AppBundle:Music\Countries');
        $builder = $repository->createQueryBuilder('c')
            ->select('c.id, c.name')
            ->groupBy('c.name')
            ->setMaxResults($maxItems);
        $countries = $builder->getQuery()->getResult();

        return $this->render('AppBundle:Catalog:index.html.twig', array(
            'artists'   => $artists,
            'genres'    => $genres,
            'years'     => $years,
            'countries' => $countries,

            'aid'  => (int)$this->input('aid'),
            'gid'  => (int)$this->input('gid'),
            'cid'  => (int)$this->input('cid'),
            'year' => (int)$this->input('year'),
        ));
    }

    protected function inputOrderField() {
        $field = trim(strtolower($this->input('sort')));
        switch ($field) {
            case 'song':
                $field = 's.name';
                break;
            case 'artist':
                $field = 'a.name';
                break;
            case 'genre':
                $field = 'g.name';
                break;
            case 'year':
                $field = 's.year';
                break;
            case 'country':
                $field = 'a.country';
                break;
            default:
                $field = null;
        }
        return $field;
    }

    protected function inputOrderDirection() {
        $default = 'DESC';
        $field = trim(strtoupper($this->input('dir', $default)));
        $field = ('DESC' === $field) ? 'DESC' : 'ASC';
        return $field;
    }

    protected function input($key, $default = null) {
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
            'itemsOnPage'   => $this->itemsPerPage,
        ];
    }

    public function getAction()
    {
        // TODO: disable access by GET request
        $em = $this->get('doctrine')->getManager('default');
        $repository = $em->getRepository('AppBundle:Music\Songs');

        $aName = $this->input('artist');
        $aId   = (int)$this->input('aid');  // Artist ID
        $gName = $this->input('genre');
        $gId   = (int)$this->input('gid');  // Genre ID
        $year  = $this->input('year');
        $cId   = (int)$this->input('cid');  // Country ID

        $builder = $repository
            ->createQueryBuilder('s')
            ->join('s.artist', 'a')
            ->join('a.genre', 'g');

        # ARTIST
        if ($aId) {
            $builder->andWhere('a.id = :aId')->setParameter(':aId', $aId);
        } else if (isset($aName[0])) {
            // TODO: search by string
            $builder->andWhere('a.name LIKE :aName')->setParameter(':aName', "$aName%");
        }

        # GENRE
        if ($gId) {
            $builder->andWhere('g.id = :gId')->setParameter(':gId', $gId);
        } else if (isset($gName[0])) {
            // TODO: search by string
            $builder->andWhere('g.name LIKE :gName')->setParameter(':gName', "$gName%");
        }

        # COUNTRY
        if ($cId) {
            $builder->join('a.country', 'c');
            $builder->andWhere('c.id = :cId')->setParameter(':cId', $cId);
        }

        # YEAR
        if (isset($year[3])) {
            $builder->andWhere('s.year = :year')->setParameter(':year', $year);
        }

        # ORDER BY
        if ($sortBy = $this->inputOrderField()) {
            $builder->orderBy($sortBy, $this->inputOrderDirection());
        }

        try {
            $pagination = $this->calculatePagination($builder);
            $items = $builder->getQuery()->getResult();
    
            $items = array_map(function ($entity) {
                $artist = $entity->getArtist();
                return [
                    'id'        => $entity->getID(),
                    'artist'    => $artist->getName(),
                    'song'      => $entity->getName(),
                    'genre'     => $artist->getGenre()->getName(),
                    'year'      => $entity->getYear(),
                    'country'   => $artist->getCountry()->getName(),
                ];
            }, $items);

            $data = compact('items', 'pagination');
        
            return $this->jsonSuccessResponse($data);
        } catch (Exception $e) {
            return $this->jsonFailureResponse([], $e->getMessage());
        }
    }

}
