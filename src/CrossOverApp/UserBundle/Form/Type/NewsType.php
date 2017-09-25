<?php

namespace CrossOverApp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('newsTitle', 'text');
        $builder->add('newDescription', 'textarea', array(
    'attr' => array('cols' => '90', 'rows' => '10'),
));
        
        $builder->add('file');
     }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('news_create')
        ));
    }

    public function getName() {
        return 'news';
    }

}

?>
