<?php declare(strict_types=1);

namespace App\Controller;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseDataTableController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    protected function createDataTableView(
        string $type, 
        Request $request, 
        mixed $query = null, 
        array $options = []
    ): DataTableView
    {
        $dataTable = $this->createDataTable($type, $query, $options);
        $dataTable->handleRequest($request);

        return $dataTable->createView();
    }
}