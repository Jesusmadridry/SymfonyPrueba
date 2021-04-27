<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="products_list")
     * Method({"GET"})
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        // $products = ['Article 1', 'Article 2'];
        return $this->render('articles/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function show($id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        
        return $this->render('articles/show.html.twig', [
                'product' => $product
                ]);
    }

    /**
     * @Route("/new/product", name="new_product")
     *Method({"GET", "POST"})
     */
    public function newProduct(Request $request){
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add('code', TextType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('name', TextType::class, array('attr' => 
            array('class' => 'form-control', 'minlength' => 4)))
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('brand', TextType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('category',  EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'name'
            ])
            ->add('price', NumberType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('createdAt', DateTimeType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('updatedAt', DateTimeType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('create', SubmitType::class, array(
                'label' => 'CREATE PRODUCT',
                'attr' => array('class' => 'btn btn-primary btn-lg')
            ))
            ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                // $product.setCategory();
                $product = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute('products_list');
            }
            
            return $this->render('articles/newArticle.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     *  @Route("/product/edit/{id}", name="edit_product")
     *  Method({"GET", "POST"})
    */
    public function editProduct(Request $request, $id){
        $product = new Product();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $form = $this->createFormBuilder($product)
            ->add('code', TextType::class, array('attr' => 
            array('class' => 'form-control', 'minlength' => 4, 'maxlength' => 10)))
            ->add('name', TextType::class, array('attr' => 
            array('class' => 'form-control', 'minlength' => 4)))
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('category',  EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'name'
            ])
            ->add('brand', TextType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('price', NumberType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('createdAt', DateTimeType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('updatedAt', DateTimeType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('create', SubmitType::class, array(
                'label' => 'EDIT PRODUCT',
                'attr' => array('class' => 'btn btn-primary btn-lg')
            ))
            ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirectToRoute('products_list');
            }
            return $this->render('articles/editArticle.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     *  @Route("/product/delete/{id}")
     *  Method({"DELETE"})
     */
    public function delete(Request $request, $id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $response = new Response();
        $response->send();
    }

    /**
     * @Route("/article/save")
     */
    public function save(){
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $product->setName('Whatever');
        $product->setCode('12Whatever');
        $em->persist($product);
        $em->flush();
        
        return new Response('Saved a products with the id of '.$product->getId());
    }

}
