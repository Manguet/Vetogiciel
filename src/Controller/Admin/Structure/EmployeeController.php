<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Employee;
use App\Form\Structure\EmployeeType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Interfaces\Slugger\SluggerInterface;
use App\Security\EmailVerifier;
use App\Service\User\PasswordEncoderServices;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/employee", name="admin_employee_")
 *
 * @Security("is_granted('ADMIN_EMPLOYEE_ACCESS')")
 */
class EmployeeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PasswordEncoderServices
     */
    private $encoderServices;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * EmployeeController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoderServices $encoderServices
     * @param FlashBagInterface $flashBag
     * @param EmailVerifier $emailVerifier
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordEncoderServices $encoderServices,
                                FlashBagInterface $flashBag, EmailVerifier $emailVerifier,
                                SluggerInterface $slugger)
    {
        $this->entityManager   = $entityManager;
        $this->encoderServices = $encoderServices;
        $this->flashBag        = $flashBag;
        $this->emailVerifier   = $emailVerifier;
        $this->slugger         = $slugger;
    }

    /**
     * @Route("", name="index")
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
            ->addFieldWithEditField($table, 'firstname',
                'Nom de l\'employé',
                'employee',
                'ADMIN_EMPLOYEE_EDIT'
            )
            ->add('lastname', TextColumn::class, [
                'visible'   => false,
            ])
            ->add('sector', TextColumn::class, [
                'label'     => 'Secteur',
                'orderable' => false,
                'render'    => function ($value, $employee) {

                    if (!$employee->getSector()->isEmpty()) {

                        $sectors = [];
                        foreach ($employee->getSector() as $sector) {
                            $sectors[] = $sector->getName();
                        }

                        $sectors = implode('/', $sectors);

                        if (strlen($sectors) > 30) {

                            return substr($sectors, 0, 30) . '...';
                        }

                        return $sectors;
                    }

                    return '';
                }
            ])
            ->add('isManager', TextColumn::class, [
                'label'     => 'Manager ?',
                'orderable' => true,
                'render'    => function ($value, $employee) {

                    if ($value) {
                        return '<i class="far fa-check-circle"></i>';
                    }

                    return '';
                }

            ]);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/employee/include/_delete-button.html.twig', [
                'entity' => 'employee'
            ])
            ->addOrderBy('lastname')
        ;

        $datatableField->createDatatableAdapter($table, Employee::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/employee/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_EMPLOYEE_ADD')")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function newEmployee(Request $request): Response
    {
        $employee = new Employee();

        $form = $this->createForm(EmployeeType::class, $employee, [
            'enablePassword' => true,
            'isShow'         => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $employee);

            if (isset($request->request->get('employee')['roles'])) {
                $employee->setRoles([$request->request->get('employee')['roles']]);
            }

            $employee->setFullNameSlugiffied(
                $this->slugger->generateSlugUrl(
                    $employee->getFirstname() . '-' . $employee->getLastname(),
                    Employee::class
                )
            );

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            /**
             * Generate a signed url and email it to the user
             */
            $this->emailVerifier->sendEmailConfirmation('app_register_verify_email', $employee,
                (new TemplatedEmail())
                    ->from(new Address('benjamin.manguet@gmail.com', 'Vetogiciel'))
                    ->to($employee->getEmail())
                    ->subject('Confirmation d\'adresse : Vetogiciel')
                    ->htmlTemplate('email/confirmation_email.html.twig')
            );

            $this->flashBag->add('warning', 'Merci de faire confirmer l\'adresse mail : ' . $employee->getEmail() . ' afin d\'accéder aux fonctionnalités.');


            return $this->redirectToRoute('admin_employee_index');
        }

        return $this->render('admin/employee/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => true,
            'isShow'         => false,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_EMPLOYEE_EDIT', employee)")
     *
     * @param Request $request
     * @param Employee $employee
     *
     * @return Response
     */
    public function editEmployee(Request $request, Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee, [
            'enablePassword' => false,
            'isShow'         => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $employee);

            if (isset($request->request->get('employee')['roles'])) {
                $employee->setRoles([$request->request->get('employee')['roles']]);
            }

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_employee_index');
        }

        return $this->render('admin/employee/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => false,
            'isShow'         => true,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_EMPLOYEE_DELETE', employee)")
     *
     * @param Employee $employee
     *
     * @return JsonResponse
     */
    public function delete(Employee $employee): JsonResponse
    {
        if (!$employee instanceof Employee) {
            return new JsonResponse('Employee Not Found', 404);
        }

        $this->entityManager->remove($employee);
        $this->entityManager->flush();

        return new JsonResponse('Employee deleted with success', 200);
    }
}