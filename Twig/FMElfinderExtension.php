<?php

namespace Toro\Bundle\MediaBundle\Twig;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Stfalcon\Bundle\TinymceBundle\Twig\Extension\StfalconTinymceExtension;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory;

class FMElfinderExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var DocumentManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $rootFolder;

    /**
     * @var StfalconTinymceExtension
     */
    protected $tinymce;

    public function __construct(
        \Twig_Environment $twig,
        ManagerRegistry $registry,
        $managerName,
        $rootFolder,
        StfalconTinymceExtension $tinymce = null
    ) {
        $this->twig = $twig;
        $this->tinymce = $tinymce;

        $this->manager = $registry->getManager($managerName);
        $this->rootFolder = $rootFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = array('is_safe' => array('html'));

        return array(
            new \Twig_SimpleFunction('toro_tinymce', array($this, 'tinymce4'), $options),
        );
    }

    /**
     * @param array $parameters
     * @param string $instance
     *
     * @throws \Twig_Error_Runtime
     *
     * @return mixed
     */
    public function tinymce4($parameters = array(), $instance = 'default')
    {

        if (!is_string($instance)) {
            throw new \Twig_Error_Runtime('The function can be applied to strings only.');
        }

        $parameters = array_merge(
            ['width' => 900, 'height' => 450, 'title' => 'Toro File Manager', 'mediaPath' => null, 'tinymce' => []],
            $parameters
        );

        if ($parameters['mediaPath']) {
            $this->checkAndCreateFolder($parameters['mediaPath']);
        }

        return $this->twig->render('ToroMediaBundle:Elfinder:_tinymce4.html.twig', array(
            'instance' => $instance,
            'width' => $parameters['width'],
            'height' => $parameters['height'],
            'title' => $parameters['title'],
            'homeFolder' => $parameters['mediaPath'],
        ))
        .$this->tinymce->tinymceInit($parameters['tinymce'])
        ;
    }

    /**
     * @param string $homeFolder
     */
    private function checkAndCreateFolder($homeFolder)
    {
        if ($this->manager->find(null, $dirname = $this->rootFolder.'/'.$homeFolder)) {
            return;
        }

        $dir = new Directory();
        $dir->setName($homeFolder);
        $dir->setId($dirname);

        $this->manager->persist($dir);
        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'toro_el_finder';
    }
}
