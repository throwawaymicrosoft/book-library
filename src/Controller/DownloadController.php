<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DownloadController extends AbstractController
{
    /**
     * @Route("/download/{id}", name="download", requirements={"id":"\d+"})
     */
    public function download(Book $book): BinaryFileResponse
    {
        if ($book->getAllowDownload() === false) {
            throw new AccessDeniedHttpException();
        }

        $response = new BinaryFileResponse(
            $this->getParameter('store.path') . '/' . $book->getFile(),
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $book->getOriginalFileName(),
        );

        return $response;
    }
}
