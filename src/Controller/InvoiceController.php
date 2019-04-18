<?php

namespace App\Controller;

use App\Zoho\Service\Books\InvoiceService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{
    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * InvoiceController constructor.
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * @Route("/invoices", name="all_invoices")
     */
    public function allInvoices()
    {
        $invoices = $this->invoiceService->getAllInvoices();

        return $this->render('books/invoice/overview.html.twig', [
            'invoices' => $invoices,
        ]);
    }
}
