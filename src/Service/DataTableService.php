<?php declare(strict_types=1);

namespace App\Service;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\HttpFoundation\RequestStack;

class DataTableService
{
    public function __construct(
        private DataTableFactoryInterface $factory,
        private RequestStack $requestStack,
    ) {
    }

    public function createView(string $type, mixed $data = null, array $options = []): DataTableView
    {
        $request = $this->requestStack->getCurrentRequest();

        $dataTable = $this->factory->create($type, $data, $options);
        $dataTable->handleRequest($request);

        return $dataTable->createView();
    }
}