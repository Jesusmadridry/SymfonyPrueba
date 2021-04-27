<?php

namespace App\Controller;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
class CategoryController extends AbstractController
{
    /**
     * @Route("/new/category", name="new_category")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new/category", name="new_category")
     *Method({"GET", "POST"})
     */
    public function newCategory(Request $request){
        $category = new Category();
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, array('attr' => 
            array('class' => 'form-control',
                  'minlength' => 4)))
            ->add('active', CheckboxType::class, array(
                'attr' => array('class' => ' form-check-input', 'minlength' => 4) 
            ))
            ->add('createdAt', DateTimeType::class, array('attr' => array(''), 
             'html5' => false))
            ->add('create', SubmitType::class, array(
                'label' => 'CREATE CATEGORY',
                'attr' => array('class' => 'btn btn-primary btn-lg mt-3')
            ))
            ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $updatedAt = $form->get('createdAt')->getData();
                $category->setUpdatedAt($updatedAt);
                $category = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                
                $this->addFlash(
                    'info',
                    'Added successfully!'
                );
                return $this->redirectToRoute('new_category');
            }
            
            return $this->render('category/newCategory.html.twig', array(
                'form' => $form->createView()
            ));
    }
}
