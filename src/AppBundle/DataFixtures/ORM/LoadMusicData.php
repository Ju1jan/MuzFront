<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Music;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMusicData implements FixtureInterface, ContainerAwareInterface
{
	private $manager;

	private $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	private function getEntityManager()
	{
		return $this->container->get('doctrine')->getEntityManager('default');
	}

	private function save($entity)
	{
		$this->manager->persist($entity);
		$this->manager->flush();
	}


	private function seedGenres()
	{
		$items = [
			'Rock',
			'HardRock',
			'Alternative rock',
			'Progressive rock',
			'Folk',
			'Punk',
			'Rap',
			'Ambient',
			'Industrial',
			'Ska',
			'Disco',
		];

		foreach ($items as $name) {
			$entity = new Music\Genres();
			$entity->setName($name);
			$this->save($entity);
		}
	}

	private function seedSongs()
	{
		/**
		 * Songs
		 */
		$em = $this->getEntityManager();
		$repository = $em->getRepository('AppBundle:Music\Artists');
		$items = [
			'Muse' => [
				'Blackout',
				'Easily',
				'Drones',
				'Fury',
			],
			'Vincent' => [
				'Ні кроку назад',
				'Веру',
				'Развага',
				'Ток',
			],
			'Depeche Mode' => [
				'Personal Jesus',
				'Enjoy the Silence',
			],
			'Akute' => [
				'Марнатраўны',
				'Бегчы',
			],
			'The Bee Gees' => [
				'Qwerty',
				'Asdfg',
			],
			'Šuma' => [
				'Sonca',
				'Ranak',
			],
			'ДДТ' => [
				'Туман',
				'Весенняя',
				'Рок-н-ролл Дядя Миша',
			],
			'СЛОТ' => [
				'Лего',
				'2 Войны',
				'Доска',
			],
			'Океан Ельзи' => [
				'Без бою',
				'Миг',
				'Сосны',
			],
			'Мій Батько П\'є' => [
				'Ти один',
				'Діти наше майбутнє',
			],
			'Coma' => [
				'Spadam',
			],
			'Lipali' => [
				'Upadam',
				'Jeżozwierz',
			],
			'Pidżama Porno' => [
				'Ezoteryczny Poznań',
			],
			'Rammstein' => [
				'Mutter',
				'Engel',
			],
			'Megaherz' => [
				'Hurra wir leben noch',
				'Menschmaschine',
			],
		];
		foreach ($items as $artist => $list) {
			$entities = $repository->findBy(array('name' => $artist));
			$artist = null;
			if (is_array($entities) && !empty($entities)) {
				$artist = reset($entities);
			}
			if (!$artist) continue;
			foreach ($list as $name) {
				$entity = new Music\Songs();
				$entity->setName($name);
				$entity->setArtistId($artist->getID());
				$entity->setYear(rand(1914, 2016));
				$this->save($entity);
			}
		}
	}

	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;

		/**
		 * Genres
		 */
		$this->seedGenres();


		/**
		 * Countries
		 */
		$items = [
			'Belarus' => 'Беларусь',
			'Russia' => 'Россия',
			'Ukraine' => 'Україна',
			'Poland' => 'Polska',
			'Germany' => 'Deutschland',
			'Britain' => 'Great Britain',
		];

		$codes = [
			'Belarus' => 'by',
			'Russia' => 'ru',
			'Ukraine' => 'ua',
			'Poland' => 'pl',
			'Germany' => 'de',
			'Britain' => 'gbr',
		];

		foreach ($items as $name => $nativeName) {
			$varName = 'country' . $name;
			$entity = new Music\Countries();
			$entity->setName($name);
			$entity->setNativeName($nativeName);
			if (isset($codes[$name])) {
				$entity->setCode($codes[$name]);
			}
			$this->save($entity);
			$$varName = $entity;
		}

		/** @var Music\Countries $countryBelarus */
		/** @var Music\Countries $countryRussia */
		/** @var Music\Countries $countryUkraine */
		/** @var Music\Countries $countryPoland */
		/** @var Music\Countries $countryGermany */
		/** @var Music\Countries $countryBritain */


		/**
		 * Artists
		 */
		$items = [
			'countryBelarus' => [
				'Vincent' => 'Rap',
				'Akute' => 'Rock',
				'Šuma' => 'Folk',
			],
			'countryRussia' => [
				'ДДТ' => 'Rock',
				'СЛОТ' => 'HardRock',
			],
			'countryUkraine' => [
				'Океан Ельзи' => 'Rock',
				'Мій Батько П\'є' => 'HardRock',
			],
			'countryPoland' => [
				'Coma' => 'Rock',
				'Lipali' => 'Alternative rock',
				'Pidżama Porno' => 'Punk',
			],
			'countryGermany' => [
				'Rammstein' => 'Disco',
				'OOMPH!' => 'Ska',
				'Megaherz' => 'Industrial',
			],
			'countryBritain' => [
				'Muse' => 'Alternative rock',
				'Depeche Mode' => 'Alternative rock',
				'The Bee Gees' => 'Ambient',
			],
		];

		$em = $this->getEntityManager();
		$repository = $em->getRepository('AppBundle:Music\Genres');

		foreach ($items as $country => $list) {
			foreach ($list as $band => $genre) {
				if (!isset($$country)) continue;
				$entities = $repository->findBy(array('name' => $genre));
				if (!array($entities) || empty($entities)) {
					continue;
				}
				$objGenre = reset($entities);

				$entity = new Music\Artists();
				$entity->setName($band);
				$entity->setGenreId($objGenre->getID());
				$entity->setCountryId($$country->getID());

				$this->save($entity);
			}
		}

		/**
		 * Songs
		 */
		$this->seedSongs();

	}

}
