<?php

namespace AppBundle\Form\Title;

use AppBundle\Entity\Format;
use AppBundle\Entity\Genre;
use AppBundle\Form\Firm\FirmFilterType;
use AppBundle\Form\Person\PersonFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Search form for titles.
 */
class TitleSearchType extends AbstractType {

    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setMethod('get');
        $em = $options['entity_manager'];
        $formats = $em->getRepository(Format::class)->findAll(array(
            'name' => 'ASC',
        ));
        $genres = $em->getRepository(Genre::class)->findAll(array(
            'name' => 'ASC',
        ));

        $builder->add('title', TextType::class, array(
            'label' => 'Title',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter all or part of a title'
            ),
        ));

        $builder->add('orderby', ChoiceType::class, array(
            'label' => 'Order by',
            'choices' => array(
                'Title' => 'title',
                'Publication Date' => 'pubdate',
            ),
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => 'title',
            'data' => 'title',
        ));

        $builder->add('orderdir', ChoiceType::class, array(
            'label' => 'Order Direction',
            'choices' => array(
                'Ascending (A to Z)' => 'asc',
                'Descending (Z to A)' => 'desc',
            ),
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => 'asc',
            'data' => 'asc',
        ));

        $builder->add('checked', ChoiceType::class, array(
            'label' => 'Checked',
            'choices' => array(
                'Yes' => 'Y',
                'No' => 'N',
            ),
            'attr' => array(
                'help_block' => 'Limit results to those that have been checked or not checked'
            ),
            'required' => false,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => null,
            'data' => null,
        ));

        $builder->add('finalcheck', ChoiceType::class, array(
            'label' => 'Double Checked',
            'choices' => array(
                'Yes' => 'Y',
                'No' => 'N',
            ),
            'attr' => array(
                'help_block' => 'Limit results to those that have been double checked or not checked'
            ),
            'required' => false,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => null,
            'data' => null,
        ));

        $builder->add('pubdate', TextType::class, array(
            'label' => 'Publication Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>)',
            ),
        ));

        $builder->add('location', TextType::class, array(
            'label' => 'Printing Location',
            'required' => false,
            'attr' => array(
                'help_block' => 'Geotagged location as indicated by the imprint'
            ),
        ));

        $builder->add('format', ChoiceType::class, array(
            'choices' => $formats,
            'choice_label' => function($value, $key, $index) {
                return $value->getName();
            },
            'choice_value' => function($value) {
                return $value->getId();
            },
            'label' => 'Format',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
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

        $builder->add('signed_author', TextType::class, array(
            'label' => 'Signed author',
            'required' => false,
            'attr' => array(
                'help_block' => 'Author attribution as it appears on the title page or at the end of the preface (Ex. “By a lady,” “By the author of“)'
            ),
        ));

        $builder->add('imprint', TextType::class, array(
            'label' => 'Imprint',
            'required' => false,
            'attr' => array(
                'help_block' => 'Information about printers, publishers, booksellers as represented on the title page'
            ),
        ));

        $builder->add('pseudonym', TextType::class, array(
            'label' => 'Pseudonym',
            'required' => false,
            'attr' => array(
                'help_block' => 'The false name that the author has signed the work with'
            ),
        ));

        $builder->add('person_filter', PersonFilterType::class, array(
            'label' => 'Filter by Person',
            'required' => false,
            'attr' => array(
                'class' => 'embedded-form'
            ),
        ));

        $builder->add('firm_filter', FirmFilterType::class, array(
            'label' => 'Filter by Firm',
            'required' => false,
            'attr' => array(
                'class' => 'embedded-form'
            ),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        $resolver->setRequired(array('entity_manager'));
    }

}
