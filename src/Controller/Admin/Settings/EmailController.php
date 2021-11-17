<?php

namespace App\Controller\Admin\Settings;

use App\Entity\Mail\Email;
use App\Form\Settings\EmailType;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/email/", name="email_")
 */
class EmailController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->kernel        = $kernel;
    }

    /**
     * @Route ("index", name="index")
     *
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('title', TextColumn::class, [
                'label'     => 'Titre',
                'orderable' => true,
                'render'    => function ($value, $email) {
                    return '<a href="/admin/email/edit/' . $email->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('description', TextColumn::class, [
                'label'     => 'Description',
                'orderable' => true,
                'render'    => function ($value) {
                    if (strlen($value) <= 60) {
                        return $value;
                    }

                    return substr($value,0,60) . ' ...';
                }
            ])
            ->add('template', TextColumn::class, [
                'label'     => 'Template utilisé',
                'orderable' => true,
            ])
            ->add('isActivated', BoolColumn::class, [
                'label'      => 'Email activé ?',
                'orderable'  => true,
                'trueValue'  => '<i class="fas fa-check"></i>',
                'falseValue' => '<i class="fas fa-times"></i>',
                'nullValue'  => '<i class="fas fa-times"></i>',
            ])
            ->add('delete', TextColumn::class, [
                'label'   => 'Supprimer ?',
                'render'  => function($value, $email) {
                    return $this->renderView('admin/settings/email/include/_delete-button.html.twig', [
                        'email' => $email,
                    ]);
                }
            ])
            ->addOrderBy('title')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Email::class
            ])
        ;

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/settings/email/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route ("new", name="new")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $email = new Email();
        $templates = $this->getTemplates();

        $form = $this->createForm(EmailType::class, $email, [
            'templates' => $templates
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $template = "{% apply inky_to_html|inline_css(source('@styles/email.css')) %}
                <container>" . $email->getTemplate() . "</container>{% endapply %}";

            $email->setTemplate($template);

            $this->entityManager->persist($email);
            $this->entityManager->flush();

            return $this->redirectToRoute('email_index');
        }

        return $this->render('admin/settings/email/new.html.twig', [
            'form'  => $form->createView(),
            'email' => $email
        ]);
    }

    /**
     * @Route ("edit/{id}", name="edit")
     *
     * @param Request $request
     * @param mixed $id
     *
     * @return Response
     */
    public function edit(Request $request, $id): Response
    {
        $email = $this->entityManager->getRepository(Email::class)
            ->find((int)$id);

        $templates = $this->getTemplates();

        $form = $this->createForm(EmailType::class, $email, [
            'templates' => $templates
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('email_index');
        }

        return $this->render('admin/settings/email/new.html.twig', [
            'form'  => $form->createView(),
            'email' => $email
        ]);
    }

    /**
     * @return array
     */
    private function getTemplates(): array
    {
        $finder = new Finder();

        $finder->files()->in($this->kernel->getProjectDir() . '/templates/email');

        if (!$finder->hasResults()) {
            throw new FileNotFoundException(
                'Aucun fichier dans ' . $this->kernel->getProjectDir() . '/templates/email'
            );
        }

        $files = [];
        foreach ($finder as $file) {
            $files[$file->getFilename()] = $file->getRelativePathname();
        }

        return $files;
    }
}