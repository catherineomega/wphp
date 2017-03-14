<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;

use AppBundle\Entity\Genre;
use AppBundle\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of FirmFilterType
 *
 * @author mjoyce
 */
class TitleFilterType extends AbstractType {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry) {
        $this->em = $registry->getManager();
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $genreRepo = $this->em->getRepository(Genre::class);
        $roleRepo = $this->em->getRepository(Role::class);
        $roles = $roleRepo->findAll(array(
            'name' => 'ASC',
        ));
        $genres = $genreRepo->findAll(array(
            'name' => 'asc'
        ));
        
        $builder->add('title', TextType::class, array(
            'label' => 'Title',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter all or part of a title.'
            ),
        ));

        $builder->add('person_role', ChoiceType::class, array(
            'choices' => $roles,
            'choice_label' => function($value, $key, $index) {
                return $value->getName();
            },
            'choice_value' => function($value) {
                if($value) {
                    return $value->getId();
                }
            },
            'label' => 'Person Role',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ));
            
        $builder->add('pubdate', TextType::class, array(
            'label' => 'Publication Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>).',
            ),
        ));

        $builder->add('genre', ChoiceType::class, array(
            'choices' => $genres,
            'choice_label' => function($value, $key, $index) {
                return $value->getName();
            },
            'choice_value' => function($value) {
                return $value->getId();
            },
            'label' => 'Genre',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ));
            
        $builder->add('location', TextType::class, array(
            'label' => 'Printing Location',
            'required' => false,
        ));
    }

}