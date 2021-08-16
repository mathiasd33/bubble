<?php


namespace App\Form;


use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('title')
           ->add('content')
           ->add('date', null,[
               'label'=>"Date de publication",
               'data'=> new \DateTime('now')
           ])
           ->add('published', CheckboxType::class,[
               'label'=>'publier ?',
               'data'=>true
           ])

           ->add('categorie',EntityType::class,[
               'class'=>Categorie::class,
               'choice_label'=>'title'
           ])

           ->add('image',FileType::class,[
               'mapped'=>false
           ])
           ->add('submit',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}