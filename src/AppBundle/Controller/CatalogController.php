<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class CatalogController extends Controller
{
    use JsonControllerTrait;

    public function indexAction()
    {
        return $this->render('AppBundle:Catalog:index.html.twig', array(
            // ...
        ));
    }

    private function input($key) {
        static $request = null;
        if (null === $request) {
            $request = Request::createFromGlobals();
        }
        return $request->query->get($key);
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
            
            $data = compact('items');
        
            return $this->jsonSuccessResponse($data);
        } catch (Exception $e) {
            return $this->jsonFailureResponse([], $e->getMessage());
        }
    }

}
