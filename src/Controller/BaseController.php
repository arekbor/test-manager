<?php declare(strict_types=1);

namespace App\Controller;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private DataTableFactoryInterface $dataTableFactory,
        private RequestStack $requestStack
    ) {
    }

    protected function createDataTable(string $type, mixed $data = null, array $options = []): DataTableView
    {
        $request = $this->requestStack->getCurrentRequest();

        $dataTable = $this->dataTableFactory->create($type, $data, $options);
        $dataTable->handleRequest($request);

        return $dataTable->createView();
    }
}