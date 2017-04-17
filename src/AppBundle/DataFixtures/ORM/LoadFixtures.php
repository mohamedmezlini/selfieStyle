<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Event;
use AppBundle\Entity\GPage;
use AppBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the sample data to load in the database when running the unit and
 * functional tests. Execute this command to load the data:
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class LoadFixtures implements FixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
      // $this->loadPosts($manager);
         $this->loadEvents($manager);
        $this->loadGPages($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        $passwordEncoder = $this->container->get('security.password_encoder');

        $mayssaUser = new User();
        $mayssaUser->setUsername('mayssa_user');
        $mayssaUser->setEmail('mayssa_user@symfony.com');
        $encodedPassword = $passwordEncoder->encodePassword($mayssaUser, 'kitten');
        $mayssaUser->setPassword($encodedPassword);
        $manager->persist($mayssaUser);

        $tayachiAdmin = new User();
        $tayachiAdmin->setUsername('tayachi_admin');
        $tayachiAdmin->setEmail('tayachi_admin@symfony.com');
        $tayachiAdmin->setRoles(['ROLE_ADMIN']);
        $encodedPassword = $passwordEncoder->encodePassword($tayachiAdmin, 'kitten');
        $tayachiAdmin->setPassword($encodedPassword);
        $manager->persist($tayachiAdmin);

        $manager->flush();
    }

  private function loadPosts(ObjectManager $manager)
    {
        foreach (range(1, 14) as $i) {
            $post = new Post();

            $post->setTitle($this->getRandomPostTitle());
            $post->setSummary($this->getRandomPostSummary());
            $post->setSlug($this->container->get('slugger')->slugify($post->getTitle()));
            $post->setContent($this->getPostContent());
            $post->setAuthorEmail('tayachi_admin@symfony.com');
            $post->setPublishedAt(new \DateTime('now - '.$i.'days'));
            $post->setDateOfEvent(new \DateTime('now - '.$i.'days'));

            foreach (range(1, 3) as $j) {
                $comment = new Comment();

                $comment->setAuthorEmail('mayssa_user@symfony.com');
                $comment->setPublishedAt(new \DateTime('now + '.($i + $j).'seconds'));
                
                $comment->setContent($this->getRandomCommentContent());
                $comment->setPost($post);

                $manager->persist($comment);
                $post->addComment($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    //--------------------med add---------------------
    //-------------------Evnent ------------------------

    private function loadEvents(ObjectManager $manager)
    {
        foreach (range(1, 14) as $i) {
            $event = new Event();

            $event->setTitle($this->getRandomPostTitle());
            $event->setSummary($this->getRandomPostSummary());
            $event->setSlug($this->container->get('slugger')->slugify($event->getTitle()));
            $event->setContent($this->getPostContent());
            $event->setAuthorEmail('tayachi_admin@symfony.com');
            $event->setPublishedAt(new \DateTime('now - '.$i.'days'));
            $event->setDateOfEvent(new \DateTime('now - '.$i.'days'));

            foreach (range(1, 3) as $j) {
                $post = new Post();

            $post->setTitle($this->getRandomPostTitle());
            $post->setSummary($this->getRandomPostSummary());
            $post->setSlug($this->container->get('slugger')->slugify($post->getTitle()));
            $post->setContent($this->getPostContent());
            $post->setAuthorEmail('tayachi_admin@symfony.com');
            $post->setPublishedAt(new \DateTime('now - '.$i.'days'));
            $post->setDateOfEvent(new \DateTime('now - '.$i.'days'));
                $post->setEvent($event);
                 foreach (range(1, 3) as $j) {
                    $comment = new Comment();

                    $comment->setAuthorEmail('mayssa_user@symfony.com');
                    $comment->setPublishedAt(new \DateTime('now + '.($i + $j).'seconds'));
                
                    $comment->setContent($this->getRandomCommentContent());
                    $comment->setPost($post);

                    $manager->persist($comment);
                    $post->addComment($comment);
                }

                $manager->persist($post);
                $event->addPost($post);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
    //------------------ GPage -------------------------


    private function loadGPages(ObjectManager $manager)
    {
        foreach (range(1, 9) as $i) {
            $gpage = new Gpage();

            $gpage->setTitle($this->getRandomPostTitle());
            $gpage->setSummary($this->getRandomPostSummary());
            $gpage->setSlug($this->container->get('slugger')->slugify($gpage->getTitle()));
            $gpage->setContent($this->getPostContent());
            $gpage->setAuthorEmail('tayachi_admin@symfony.com');
            $gpage->setPublishedAt(new \DateTime('now - '.$i.'days'));

        
            foreach (range(1, 3) as $j) {
                $post = new Post();

            $post->setTitle($this->getRandomPostTitle());
            $post->setSummary($this->getRandomPostSummary());
            $post->setSlug($this->container->get('slugger')->slugify($post->getTitle()));
            $post->setContent($this->getPostContent());
            $post->setAuthorEmail('tayachi_admin@symfony.com');
            $post->setPublishedAt(new \DateTime('now - '.$i.'days'));
            $post->setDateOfEvent(new \DateTime('now - '.$i.'days'));
            //$post->setEvent($event = new Event());

                $post->setGPage($gpage);

                 foreach (range(1, 3) as $j) {
                    $comment = new Comment();

                    $comment->setAuthorEmail('mayssa_user@symfony.com');
                    $comment->setPublishedAt(new \DateTime('now + '.($i + $j).'seconds'));
                
                    $comment->setContent($this->getRandomCommentContent());
                    $comment->setPost($post);

                    $manager->persist($comment);
                    $post->addComment($comment);
                }

                $manager->persist($post);
                $gpage->addPost($post);
            }

            $manager->persist($gpage);
        }

        $manager->flush();
    }

    //------------------------ fin GPage ------------

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function getPostContent()
    {
        return <<<MARKDOWN
Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
incididunt ut labore et **
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
deserunt mollit anim id est laborum.

Aliquam tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
tincidunt, faucibus nisl in, aliquet libero.
MARKDOWN;
    }

    private function getPhrases()
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
        ];
    }

    private function getRandomPostTitle()
    {
        $titles = $this->getPhrases();

        return $titles[array_rand($titles)];
    }

    private function getRandomPostSummary($maxLength = 255)
    {
        $phrases = $this->getPhrases();

        $numPhrases = mt_rand(6, 12);
        shuffle($phrases);

        return substr(implode(' ', array_slice($phrases, 0, $numPhrases-1)), 0, $maxLength);
    }

    private function getRandomCommentContent()
    {
        $phrases = $this->getPhrases();

        $numPhrases = mt_rand(2, 15);
        shuffle($phrases);

        return implode(' ', array_slice($phrases, 0, $numPhrases-1));
    }
}
