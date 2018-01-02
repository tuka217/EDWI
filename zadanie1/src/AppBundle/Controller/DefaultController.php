<?php

namespace AppBundle\Controller;

use AppBundle\Form\AddressType;
use AppBundle\Form\AlgorithmType;
use AppBundle\Form\DirectoryType;
use AppBundle\Form\HyperlinkType;
use AppBundle\Form\QueryType;
use AppBundle\Helper\BookProcessor;
use AppBundle\Helper\FileSaver;
use AppBundle\Helper\HyperlinkParser;
use AppBundle\Helper\HyperlinkParserRecursion;
use AppBundle\Helper\QueryHelper;
use AppBundle\Math\AlgorithmCalculator;
use AppBundle\Math\ResemblanceCalculator;
use AppBundle\Model\Address;
use AppBundle\Model\Algorithm;
use AppBundle\Model\Directory;
use AppBundle\Model\Hyperlink;
use AppBundle\Model\Query;
use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

set_time_limit (10800);

class DefaultController extends Controller
{
    public function menuAction(Request $request)
    {
        return $this->render('default/menu.html.twig', [
        ]);
    }

    public function passAddressAction(Request $request)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Address $address */
            $address = $form->getData();

            $client = new Client();
            $res = $client->request('GET', $address->getAddress(), []);

            /** @var StreamInterface $streamedBody */
            $streamedBody = $res->getBody();

            $pageAddress = $address->getAddress();
            $fileName = preg_replace("/[^A-Za-z0-9]/", "", $pageAddress);

            /** @var FileSaver $fileSaver */
            $fileSaver = $this->container->get('app.helper.file_saver');

            $fileSaver->saveToHtmlFile($streamedBody, $fileName);
            $fileSaver->saveToTxtFile($fileName);
        }

        return $this->render('default/pass_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function runAlgorithmAction(Request $request)
    {
        $algorithm = new Algorithm();
        $form = $this->createForm(AlgorithmType::class, $algorithm, ['dir_scanner' => $this->container->get('app.helper.dir_scanner')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Algorithm $algorithm */
            $algorithm = $form->getData();

            /** @var AlgorithmCalculator $algorithmCalculator */
            $algorithmCalculator = $this->container->get('app.math.algorithm_calculator');

            $results = $algorithmCalculator->calculate((int) $algorithm->getK(), (int) $algorithm->getThresh(), $algorithm->getFileName());

            return $this->render('default/result.html.twig', [
                'results' => $results,
                'microtime' => $algorithmCalculator->getMicrotime(),
            ]);
        }

        return $this->render('default/run_algorithm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function documentsResemblanceAction()
    {
        /** @var ResemblanceCalculator $resemblanceCalculator */
        $resemblanceCalculator = $this->container->get('app.calculator.resemblance');

        $resemblanceCalculator->runResemblanceCalculations();

        return $this->render('default/documents_resemblance.html.twig', [
            'common' => $resemblanceCalculator->returnTenTheMostCommon(),
            'different' => $resemblanceCalculator->returnTenTheMostDifferent()
        ]);
    }

    public function hyperlinkAction(Request $request)
    {
        /** @var HyperlinkParser $hyperlinkParser */
        $hyperlinkParser = $this->container->get('app.parser.hyperlink');

        $hyperlink = new Hyperlink();
        $form = $this->createForm(HyperlinkType::class, $hyperlink);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Hyperlink $hyperlink */
            $hyperlink = $form->getData();

            $pageAddress = $hyperlink->getAddress();

            $client = new Client();
            $res = $client->request('GET', $pageAddress, []);

            /** @var StreamInterface $streamedBody */
            $streamedBody = $res->getBody();

            $fileName = preg_replace("/[^A-Za-z0-9]/", "", $pageAddress);

            /** @var FileSaver $fileSaver */
            $fileSaver = $this->container->get('app.helper.file_saver');

            $fileSaver->saveToHtmlFile($streamedBody, $fileName);

            $hyperlinkParser->saveToHyperLinkFile($fileName, $pageAddress);

            /** @var HyperlinkParserRecursion $hyperlinkParserRecursion */
            $hyperlinkParserRecursion = $this->container->get('app.helper.hyperlink_parser_recursion');

            $hyperlinkParserRecursion->loadInternalFile($fileName, $hyperlink->getNumberOfRecursion(), $pageAddress);

            return $this->render('default/menu.html.twig', []);
        }

        return $this->render('default/pass_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function processBookAction(Request $request)
    {
        $directory = new Directory();
        $form = $this->createForm(DirectoryType::class, $directory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Directory $directory */
            $directory = $form->getData();

            /** @var BookProcessor $bookProcessor */
            $bookProcessor = $this->container->get('app.helper.book_processor');

            $bookProcessor->parseBooks($directory->getPathToDir());

            return $this->render('default/menu.html.twig', []);
        }

        return $this->render('default/pass_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function findBooksAction(Request $request)
    {
        $query = new Query();
        $form = $this->createForm(QueryType::class, $query);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Query $query */
            $query = $form->getData();

            /** @var QueryHelper $queryHelper */
            $queryHelper = $this->container->get('app.helper.query');

            $result = $queryHelper->findBooks($query->getKeyWords());

            return $this->render('default/find_book.html.twig', [
                'books' => $result
            ]);
        }

        return $this->render('default/pass_address.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
