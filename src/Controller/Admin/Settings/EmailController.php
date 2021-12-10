<?php

namespace App\Controller\Admin\Settings;

use App\Entity\Mail\Email;
use App\Form\Settings\EmailType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/email/", name="email_")
 *
 * @Security("is_granted('ADMIN_EMAIL_ACCESS')")
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
     * @param DatatableFieldInterface $datatableField
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,
                          DatatableFieldInterface $datatableField): Response
    {
        $table = $dataTableFactory->create();

        $datatableField
            ->addFieldWithEditField($table, 'title',
                'Titre',
                'email',
            'ADMIN_EMAIL_EDIT')
            ->add('description', TextColumn::class, [
                'label'     => 'Description',
                'orderable' => true,
                'render'    => function ($value) {
                    if (strlen($value) <= 30) {
                        return $value;
                    }

                    return substr($value,0,30) . ' ...';
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
            ]);
        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/settings/email/include/_delete-button.html.twig', [
                'entity' => 'email'
            ])
            ->addOrderBy('title')
        ;

        $datatableField
            ->createDatatableAdapter($table, Email::class);

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

        $finder->files()
            ->in($this->kernel->getProjectDir() . '/templates/email')
            ->exclude('demo')
        ;

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

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Email $email
     *
     * @return JsonResponse
     */
    public function delete(Email $email): JsonResponse
    {
        if (!$email instanceof Email) {
            return new JsonResponse('Email Not Found', 404);
        }

        $this->entityManager->remove($email);
        $this->entityManager->flush();

        return new JsonResponse('Email deleted with success', 200);
    }

    /**
     * @Route("generate", name="generate")
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function generate(): JsonResponse
    {
        $command = new Application($this->kernel);
        $command->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'import:mail:templates',
        ]);

        $output = new NullOutput();
        $command->run($input, $output);

        return new JsonResponse('Template mail importés');
    }
}