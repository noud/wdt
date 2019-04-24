<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Form\Data\PostAttachmentData;
use App\Form\Handler\PostAttachmentHandler;
use App\Form\Type\PostAttachmentType;
use App\Service\AttachmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AttachmentController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AttachmentService
     */
    //private $attachmentService;

    public function __construct(
        EntityManagerInterface $entityManager
        //AttachmentService $attachmentService
    ) {
        $this->entityManager = $entityManager;
        //$this->attachmentService = $attachmentService;
    }

    /**
     * @Route("/attachment/post/{id}", methods={"POST"}, name="attachment_post")
     */
    public function post(
        Request $request,
        PostAttachmentHandler $formHandler,
        int $id
    ): Response {
        $data = new PostAttachmentData();

        $form = $this->createForm(PostAttachmentType::class, $data);
        if ($formHandler->handleRequest($form, $request, $id)) {
            return new Response('', 201);
        }

        return new JsonResponse(
                [
                    'error' => 'Upload is waarschijnlijk het verkeerde bestandstype.',
                ],
                Response::HTTP_BAD_REQUEST
            );
    }

    /**
     * @Route("/attachment/remove/{id}", methods={"POST"}, name="attachment_remove")
     */
    public function remove(Request $request, int $id): Response
    {
        // pipeline
        $id = $id;

        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);

        $params = $request->request->all();
        foreach ($params as $key => $param) {
            if ('filename' === $key) {
                // pipeline
                $param = $param;

//                $fileName = $param;
                break;
            }
        }

//         $attachment = $this->attachmentService->getAttachmentByOfferRequestSupplierAndFileName(
//             $offerRequestSupplier,
//             $fileName
//         );
        //$this->denyAccessUnlessGranted('DOWNLOAD_SUPPLIER_ATTACHMENT', $attachment);
        //$this->attachmentService->remove($attachment);
        //$this->entityManager->flush();

        return new Response('', 201);
    }
}
